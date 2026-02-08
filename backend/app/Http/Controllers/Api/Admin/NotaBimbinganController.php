<?php

namespace App\Http\Controllers\Api\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Pembimbing;
use App\Models\Bimbingan;

class NotaBimbinganController extends Controller
{
    public function index(Request $request)
    {
        // Get pembimbing records where bimbingan is complete (status = selesai)
        $query = Pembimbing::with([
            'dosen',
            'skripsi.mahasiswa',
            'skripsi.bimbingan'
        ])->whereHas('skripsi', function ($q) {
            $q->where('status', 'bimbingan');
        });

        if ($request->filled('search')) {
            $search = $request->search;
            $query->whereHas('skripsi.mahasiswa', function ($q) use ($search) {
                $q->where('nama', 'like', "%{$search}%")
                  ->orWhere('nim', 'like', "%{$search}%");
            });
        }

        $pembimbings = $query->orderBy('created_at', 'desc')->paginate(10);

        // Transform data
        $pembimbings->getCollection()->transform(function ($item) {
            $bimbinganList = $item->skripsi?->bimbingan ?? collect();
            $selesai = $bimbinganList->where('status', 'disetujui')->count();
            $total = $bimbinganList->count();

            $item->status = $total >= 8 && $selesai >= 8 ? 'selesai' : 'proses';
            $item->tanggal_selesai = $bimbinganList->where('status', 'disetujui')
                ->sortByDesc('tanggal')
                ->first()?->tanggal;

            return $item;
        });

        // Filter only completed ones
        if ($request->filled('status') && $request->status === 'selesai') {
            $pembimbings->setCollection(
                $pembimbings->getCollection()->where('status', 'selesai')
            );
        }

        // Calculate stats
        $stats = [
            'siap_cetak' => Pembimbing::whereHas('skripsi.bimbingan', function ($q) {
                $q->where('status', 'disetujui');
            }, '>=', 8)->count(),
            'menunggu_upload' => Pembimbing::whereHas('skripsi.bimbingan', function ($q) {
                $q->where('status', 'disetujui');
            }, '<', 8)->count(),
            'total_diterbitkan' => 0, // This would track printed notas
        ];

        return response()->json([
            'success' => true,
            'data' => $pembimbings,
            'stats' => $stats
        ]);
    }
}
