<?php

namespace App\Http\Controllers\Api\Admin;

use App\Http\Controllers\Controller;
use App\Models\Dokumen;
use App\Models\Seminar;
use App\Models\Skripsi;
use App\Models\SkripsiHistory;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class SkripsiVerificationController extends Controller
{
    /**
     * List all pending verifications
     * Staff/admin only see verifications matching their gender
     * Super admin sees all
     */
    public function index(Request $request)
    {
        $query = SkripsiHistory::with(['skripsi.mahasiswa', 'updatedBy'])
            ->where('verification_status', 'pending')
            ->orderBy('created_at', 'desc');

        // Gender-based filtering for non-super_admin
        $user = $request->user();
        if ($user->role !== 'super_admin') {
            $gender = $user->jenis_kelamin;
            if ($gender) {
                // Only show verifications from staff of the same gender
                $query->where(function ($q) use ($gender) {
                    $q->whereHas('updatedBy', function ($uq) use ($gender) {
                        $uq->where('jenis_kelamin', $gender);
                    })
                        // Also show verifications from users without gender set (legacy)
                        ->orWhereHas('updatedBy', function ($uq) {
                            $uq->whereNull('jenis_kelamin');
                        });
                });

                // Also filter by mahasiswa gender (same gender only)
                $query->whereHas('skripsi.mahasiswa', function ($mq) use ($gender) {
                    $mq->where('jenis_kelamin', $gender);
                });
            }
        }

        $pending = $query->paginate($request->get('per_page', 15));

        return response()->json([
            'success' => true,
            'data' => $pending
        ]);
    }

    /**
     * Approve a pending verification
     */
    public function approve(string $id)
    {
        $history = SkripsiHistory::findOrFail($id);

        if ($history->verification_status !== 'pending') {
            return response()->json([
                'success' => false,
                'message' => 'Data ini sudah diproses sebelumnya.'
            ], 400);
        }

        DB::beginTransaction();
        try {
            // Apply changes to Skripsi
            $skripsi = $history->skripsi;

            $skripsi->judul = $history->judul_baru;
            $skripsi->status = $history->status_baru;
            $skripsi->progress_percentage = $this->calculateProgress($history->status_baru);

            $skripsi->save();

            // If status changes to lulus, also update sidang seminar hasil
            if ($history->status_baru === 'lulus') {
                Seminar::where('skripsi_id', $skripsi->id)
                    ->where('jenis', 'sidang')
                    ->where('hasil', 'lulus_revisi')
                    ->update(['hasil' => 'lulus']);
            }

            // Update history status
            $history->verification_status = 'approved';
            $history->save();

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Perubahan berhasil disetujui dan diterapkan.'
            ]);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'success' => false,
                'message' => 'Gagal menyetujui perubahan: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Reject a pending verification
     */
    public function reject(string $id)
    {
        $history = SkripsiHistory::findOrFail($id);

        if ($history->verification_status !== 'pending') {
            return response()->json([
                'success' => false,
                'message' => 'Data ini sudah diproses sebelumnya.'
            ], 400);
        }

        $history->verification_status = 'rejected';
        $history->save();

        return response()->json([
            'success' => true,
            'message' => 'Perubahan telah ditolak.'
        ]);
    }

    /**
     * Bulk approve multiple pending verifications
     */
    public function bulkApprove(Request $request)
    {
        $request->validate([
            'ids' => 'required|array|min:1',
            'ids.*' => 'integer|exists:skripsi_history,id',
        ]);

        $approved = 0;
        $skipped = 0;

        DB::beginTransaction();
        try {
            foreach ($request->ids as $id) {
                $history = SkripsiHistory::find($id);
                if (!$history || $history->verification_status !== 'pending') {
                    $skipped++;
                    continue;
                }

                $skripsi = $history->skripsi;
                $skripsi->judul = $history->judul_baru;
                $skripsi->status = $history->status_baru;
                $skripsi->progress_percentage = $this->calculateProgress($history->status_baru);
                $skripsi->save();

                // If status changes to lulus, also update sidang seminar hasil
                if ($history->status_baru === 'lulus') {
                    Seminar::where('skripsi_id', $skripsi->id)
                        ->where('jenis', 'sidang')
                        ->where('hasil', 'lulus_revisi')
                        ->update(['hasil' => 'lulus']);
                }

                $history->verification_status = 'approved';
                $history->save();
                $approved++;
            }

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => "{$approved} perubahan berhasil disetujui." . ($skipped > 0 ? " {$skipped} dilewati." : ''),
            ]);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'success' => false,
                'message' => 'Gagal memproses: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Bulk reject multiple pending verifications
     */
    public function bulkReject(Request $request)
    {
        $request->validate([
            'ids' => 'required|array|min:1',
            'ids.*' => 'integer|exists:skripsi_history,id',
        ]);

        $rejected = 0;

        foreach ($request->ids as $id) {
            $history = SkripsiHistory::find($id);
            if (!$history || $history->verification_status !== 'pending') {
                continue;
            }
            $history->verification_status = 'rejected';
            $history->save();
            $rejected++;
        }

        return response()->json([
            'success' => true,
            'message' => "{$rejected} perubahan telah ditolak.",
        ]);
    }

    /**
     * Calculate progress percentage based on status (Duplicated from SkripsiController)
     *Ideally this should be in a Service or Model
     */
    private function calculateProgress(string $status): int
    {
        $progressMap = [
            'draft' => 0,
            'pengajuan' => 5,
            'disetujui' => 10,
            'ditolak' => 0,
            'proposal' => 15,
            'sempro' => 25,
            'penentuan_mentor' => 12,
            'mentor' => 14,
            'penentuan_dospem' => 30,
            'dospem' => 40,
            'bimbingan' => 50,
            'pengajuan_sidang' => 60,
            'pengajuan_sidang_acc' => 65,
            'pengajuan_sidang_tolak' => 50,
            'semhas' => 70,
            'ujian' => 80,
            'sidang' => 85,
            'revisi' => 90,
            'lulus' => 100,
        ];

        return $progressMap[$status] ?? 0;
    }
}
