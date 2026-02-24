<?php

namespace App\Http\Controllers\Api\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Skripsi;
use App\Models\Dokumen;

class SKTugasController extends Controller
{
    public function index(Request $request)
    {
        $query = Skripsi::with([
            'mahasiswa',
            'pembimbing.dosen',
            'dokumen'
        ])->has('pembimbing'); // Only skripsi that have pembimbing assigned

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->whereHas('mahasiswa', function ($mq) use ($search) {
                    $mq->where('nama', 'like', "%{$search}%")
                        ->orWhere('nim', 'like', "%{$search}%");
                })->orWhere('judul', 'like', "%{$search}%");
            });
        }

        // Filter by status (based on SK Tugas document)
        if ($request->filled('status')) {
            if ($request->status === 'semua_sk') {
                $query->whereHas('dokumen', function ($q) {
                    $q->where('jenis', 'sk_tugas');
                });
            } elseif ($request->status === 'belum_ttd') {
                $query->whereHas('dokumen', function ($q) {
                    $q->where('jenis', 'sk_tugas')->where('status', 'pending');
                });
            } elseif ($request->status === 'sudah_ttd') {
                $query->whereHas('dokumen', function ($q) {
                    $q->where('jenis', 'sk_tugas')->where('status', 'approved');
                });
            }
        }

        $skripsi = $query->orderBy('created_at', 'desc')->paginate(10);

        // Transform data
        // Transform data
        $skripsi->getCollection()->transform(function ($item) {
            $skDoc = $item->dokumen?->where('jenis', 'sk_tugas')->sortByDesc('versi')->first();
            $item->sk_status = $skDoc ? $skDoc->status : 'belum_ada';
            $item->sk_dokumen = $skDoc; // Attach document for file access
            return $item;
        });

        return response()->json([
            'success' => true,
            'data' => $skripsi
        ]);
    }

    public function update(Request $request, $id)
    {
        $skripsi = Skripsi::findOrFail($id);

        // Update or create SK Tugas document
        $dokumen = Dokumen::updateOrCreate(
            [
                'skripsi_id' => $skripsi->id,
                'jenis' => 'sk_tugas'
            ],
            [
                'nama_file' => $request->nama_file ?? 'SK Tugas',
                'path' => $request->path ?? '',
                'status' => $request->status ?? 'selesai',
            ]
        );

        // Auto-update skripsi status to bimbingan when SK Tugas is approved
        $status = $request->status ?? 'selesai';
        if (in_array($status, ['approved', 'selesai'])) {
            \App\Models\Skripsi::where('id', $skripsi->id)
                ->whereIn('status', ['pengajuan', 'disetujui', 'penentuan_dospem', 'dospem'])
                ->update(['status' => 'bimbingan', 'progress_percentage' => 50]);
        }

        return response()->json([
            'success' => true,
            'message' => 'SK Tugas berhasil diperbarui',
            'data' => $dokumen
        ]);
    }
}
