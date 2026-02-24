<?php

namespace App\Http\Controllers\Api\Admin;

use App\Http\Controllers\Controller;
use App\Models\Dokumen;
use App\Models\Skripsi;
use App\Models\SkripsiHistory;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class SkripsiVerificationController extends Controller
{
    /**
     * List all pending verifications
     */
    public function index(Request $request)
    {
        $query = SkripsiHistory::with(['skripsi.mahasiswa', 'updatedBy'])
            ->where('verification_status', 'pending')
            ->orderBy('created_at', 'desc');

        $pending = $query->paginate($request->get('per_page', 15));

        return response()->json([
            'success' => true,
            'data' => $pending
        ]);
    }

    /**
     * Approve a pending verification
     */
    public function approve($id)
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
    public function reject($id)
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
            'penentuan_dospem' => 30,
            'dospem' => 40,
            'bimbingan' => 50,
            'pengajuan_sidang' => 60,
            'semhas' => 70,
            'ujian' => 80,
            'sidang' => 85,
            'revisi' => 90,
            'lulus' => 100,
        ];

        return $progressMap[$status] ?? 0;
    }
}
