<?php

namespace App\Http\Controllers\Api\Dosen;

use App\Http\Controllers\Controller;
use App\Models\Skripsi;
use App\Models\Bimbingan;
use Illuminate\Http\Request;

class BimbinganController extends Controller
{
    /**
     * List all mahasiswa bimbingan
     */
    public function index(Request $request)
    {
        $dosen = $request->user()->dosen;

        $query = Skripsi::with(['mahasiswa.prodi', 'bimbingan' => function ($q) {
                $q->orderBy('tanggal', 'desc');
            }])
            ->whereHas('pembimbing', function ($q) use ($dosen) {
                $q->where('dosen_id', $dosen->id)->where('is_active', true);
            })
            ->where('is_active', true);

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('judul', 'like', "%{$search}%")
                    ->orWhereHas('mahasiswa', function ($mhs) use ($search) {
                        $mhs->where('nama', 'like', "%{$search}%")
                            ->orWhere('nim', 'like', "%{$search}%");
                    });
            });
        }

        $skripsi = $query->orderBy('updated_at', 'desc')->get();

        return response()->json([
            'success' => true,
            'data' => $skripsi
        ]);
    }

    /**
     * Show detail of specific skripsi
     */
    public function show(Request $request, Skripsi $skripsi)
    {
        $dosen = $request->user()->dosen;

        // Verify dosen is pembimbing
        $isPembimbing = $skripsi->pembimbing()->where('dosen_id', $dosen->id)->exists();
        if (!$isPembimbing) {
            return response()->json([
                'success' => false,
                'message' => 'Anda bukan pembimbing skripsi ini'
            ], 403);
        }

        $skripsi->load([
            'mahasiswa.prodi',
            'pembimbing.dosen',
            'bimbingan' => function ($q) {
                $q->orderBy('tanggal', 'desc');
            },
            'seminar.penguji.dosen',
            'dokumen',
            'nilai'
        ]);

        return response()->json([
            'success' => true,
            'data' => $skripsi
        ]);
    }

    /**
     * Get bimbingan logs for a skripsi
     */
    public function logs(Request $request, Skripsi $skripsi)
    {
        $dosen = $request->user()->dosen;

        $logs = Bimbingan::with('dosen')
            ->where('skripsi_id', $skripsi->id)
            ->orderBy('tanggal', 'desc')
            ->get();

        return response()->json([
            'success' => true,
            'data' => $logs
        ]);
    }

    /**
     * Approve or reject bimbingan log
     */
    public function updateStatus(Request $request, Bimbingan $bimbingan)
    {
        $request->validate([
            'status' => 'required|in:approved,revision,rejected',
            'catatan_dosen' => 'nullable|string',
        ]);

        $dosen = $request->user()->dosen;

        // Verify dosen is pembimbing
        $isPembimbing = $bimbingan->skripsi->pembimbing()
            ->where('dosen_id', $dosen->id)->exists();
        if (!$isPembimbing) {
            return response()->json([
                'success' => false,
                'message' => 'Anda bukan pembimbing skripsi ini'
            ], 403);
        }

        $bimbingan->status = $request->status;
        $bimbingan->catatan_dosen = $request->catatan_dosen;
        $bimbingan->dosen_id = $dosen->id;
        $bimbingan->save();

        return response()->json([
            'success' => true,
            'message' => 'Status bimbingan berhasil diperbarui',
            'data' => $bimbingan
        ]);
    }
}
