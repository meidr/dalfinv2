<?php

namespace App\Http\Controllers\Api\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Seminar;
use App\Models\BeritaAcara;

class BeritaAcaraController extends Controller
{
    public function index(Request $request)
    {
        $query = Seminar::with([
            'skripsi.mahasiswa',
            'skripsi.pembimbing.dosen',
            'beritaAcara'
        ])->whereIn('status', ['selesai', 'pending'])
          ->whereIn('jenis', ['seminar_proposal', 'seminar_hasil', 'ujian']);

        if ($request->filled('search')) {
            $search = $request->search;
            $query->whereHas('skripsi.mahasiswa', function ($q) use ($search) {
                $q->where('nama', 'like', "%{$search}%")
                  ->orWhere('nim', 'like', "%{$search}%");
            });
        }

        if ($request->filled('status')) {
            if ($request->status === 'belum_cetak') {
                $query->whereDoesntHave('beritaAcara');
            } elseif ($request->status === 'selesai') {
                $query->whereHas('beritaAcara');
            }
        }

        $seminars = $query->orderBy('tanggal', 'desc')->paginate(10);

        // Add tanggal_ujian and waktu_ujian aliases for frontend compatibility
        $seminars->getCollection()->transform(function ($item) {
            $item->tanggal_ujian = $item->tanggal;
            $item->waktu_ujian = $item->waktu;
            $item->berita_acara_tercetak = $item->beritaAcara !== null;
            return $item;
        });

        // Calculate stats
        $stats = [
            'siap_generate' => Seminar::where('status', 'selesai')
                ->whereIn('hasil', ['lulus', 'lulus_revisi'])
                ->whereDoesntHave('beritaAcara')
                ->count(),
            'sudah_cetak' => BeritaAcara::count(),
            'total' => Seminar::whereIn('jenis', ['seminar_proposal', 'seminar_hasil', 'ujian'])->count(),
        ];

        return response()->json([
            'success' => true,
            'data' => $seminars,
            'stats' => $stats
        ]);
    }
}
