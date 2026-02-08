<?php

namespace App\Http\Controllers\Api\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Seminar;
use App\Models\Skripsi;
use Carbon\Carbon;

class SKYudisiumController extends Controller
{
    public function index(Request $request)
    {
        // Get seminars (ujian) that are completed with 'lulus' status
        $query = Seminar::with([
            'skripsi.mahasiswa',
            'skripsi.pembimbing.dosen'
        ])->where('jenis', 'ujian')
          ->where('status', 'selesai')
          ->whereIn('hasil', ['lulus', 'lulus_revisi']);

        if ($request->filled('search')) {
            $search = $request->search;
            $query->whereHas('skripsi.mahasiswa', function ($q) use ($search) {
                $q->where('nama', 'like', "%{$search}%")
                  ->orWhere('nim', 'like', "%{$search}%");
            });
        }

        $seminars = $query->orderBy('tanggal', 'desc')->paginate(10);

        // Transform data
        $seminars->getCollection()->transform(function ($item) {
            // Check if SK Yudisium exists
            $skYudisium = $item->skripsi?->dokumen?->where('jenis', 'sk_yudisium')->first();
            $item->tanggal_ujian = $item->tanggal;
            $item->status = $skYudisium ? 'sk_terbit' : 'siap_yudisium';
            return $item;
        });

        // Calculate stats
        $currentYear = Carbon::now()->year;
        $currentMonth = Carbon::now()->month;

        $stats = [
            'siap_yudisium' => Seminar::where('jenis', 'ujian')
                ->where('status', 'selesai')
                ->whereIn('hasil', ['lulus', 'lulus_revisi'])
                ->whereDoesntHave('skripsi.dokumen', function ($q) {
                    $q->where('jenis', 'sk_yudisium');
                })->count(),
            'sk_terbit_bulan_ini' => Skripsi::whereHas('dokumen', function ($q) use ($currentMonth, $currentYear) {
                $q->where('jenis', 'sk_yudisium')
                  ->whereMonth('created_at', $currentMonth)
                  ->whereYear('created_at', $currentYear);
            })->count(),
            'total_lulusan' => Skripsi::whereHas('dokumen', function ($q) use ($currentYear) {
                $q->where('jenis', 'sk_yudisium')
                  ->whereYear('created_at', $currentYear);
            })->count(),
            'persentase_kenaikan' => 12, // Placeholder
        ];

        return response()->json([
            'success' => true,
            'data' => $seminars,
            'stats' => $stats
        ]);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'skripsi_id' => 'required|exists:skripsi,id',
            'nomor_sk' => 'required|string',
            'tanggal' => 'required|date',
            'ipk' => 'required|numeric|min:0|max:4',
            'predikat' => 'required|string|in:memuaskan,sangat_memuaskan,cum_laude',
        ]);

        $skripsi = Skripsi::findOrFail($validated['skripsi_id']);

        // Create SK Yudisium document record
        $dokumen = $skripsi->dokumen()->create([
            'jenis' => 'sk_yudisium',
            'nama_file' => 'SK Yudisium - ' . $validated['nomor_sk'],
            'path' => '',
            'status' => 'selesai',
        ]);

        // Update skripsi status
        $skripsi->update(['status' => 'lulus']);

        // Update mahasiswa with yudisium info
        $skripsi->mahasiswa()->update([
            'ipk' => $validated['ipk'],
            'status' => 'lulus',
        ]);

        return response()->json([
            'success' => true,
            'message' => 'SK Yudisium berhasil diproses',
            'data' => $dokumen
        ], 201);
    }
}
