<?php

namespace App\Http\Controllers\Api\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Seminar;
use App\Models\Skripsi;
use App\Models\SKYudisium;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class SKYudisiumController extends Controller
{
    public function index(Request $request)
    {
        // Get seminars (ujian) that are completed with 'lulus' status
        $query = Seminar::with([
            'skripsi.mahasiswa.prodi',
            'skripsi.pembimbing.dosen',
            'skripsi.skYudisium',
            'penguji.dosen',
            'beritaAcara'
        ])->where('jenis', 'sidang')
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

        // Transform data - set status based on SKYudisium existence
        $seminars->getCollection()->transform(function ($item) {
            $item->tanggal_ujian = $item->tanggal;
            $item->sk_yudisium = $item->skripsi?->skYudisium;
            $item->status = $item->skripsi?->skYudisium ? 'sk_terbit' : 'siap_yudisium';
            return $item;
        });

        // Calculate stats
        $currentYear = Carbon::now()->year;
        $currentMonth = Carbon::now()->month;

        $lulusQuery = Seminar::where('jenis', 'sidang')
            ->where('status', 'selesai')
            ->whereIn('hasil', ['lulus', 'lulus_revisi']);

        $siapYudisium = (clone $lulusQuery)->whereHas('skripsi', function ($q) {
            $q->whereDoesntHave('skYudisium');
        })->count();

        $skTerbitBulanIni = SKYudisium::whereMonth('tanggal_terbit', $currentMonth)
            ->whereYear('tanggal_terbit', $currentYear)
            ->count();

        $totalLulusan = SKYudisium::whereYear('tanggal_terbit', $currentYear)->count();

        // Calculate percentage increase vs last year
        $lastYearTotal = SKYudisium::whereYear('tanggal_terbit', $currentYear - 1)->count();
        $persentase = $lastYearTotal > 0
            ? round((($totalLulusan - $lastYearTotal) / $lastYearTotal) * 100)
            : ($totalLulusan > 0 ? 100 : 0);

        $stats = [
            'siap_yudisium' => $siapYudisium,
            'sk_terbit_bulan_ini' => $skTerbitBulanIni,
            'total_lulusan' => $totalLulusan,
            'persentase_kenaikan' => $persentase,
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

        // Check if already exists
        if ($skripsi->skYudisium) {
            return response()->json([
                'success' => false,
                'message' => 'SK Yudisium sudah pernah diterbitkan untuk skripsi ini'
            ], 422);
        }

        $skYudisium = DB::transaction(function () use ($skripsi, $validated) {
            // Create SK Yudisium record
            $skYudisium = SKYudisium::create([
                'skripsi_id' => $skripsi->id,
                'nomor_sk' => $validated['nomor_sk'],
                'tanggal_terbit' => $validated['tanggal'],
                'tanggal_yudisium' => $validated['tanggal'],
                'predikat' => $validated['predikat'],
                'ipk_akhir' => $validated['ipk'],
            ]);

            // Update skripsi status
            $skripsi->update(['status' => 'lulus']);

            // Update mahasiswa status
            $skripsi->mahasiswa()->update([
                'status' => 'lulus',
            ]);

            return $skYudisium;
        });

        return response()->json([
            'success' => true,
            'message' => 'SK Yudisium berhasil diproses',
            'data' => $skYudisium
        ], 201);
    }

    public function exportExcel(Request $request)
    {
        $query = Seminar::with([
            'skripsi.mahasiswa.prodi',
            'skripsi.pembimbing.dosen',
            'skripsi.skYudisium',
            'penguji.dosen',
        ])->where('jenis', 'sidang')
            ->where('status', 'selesai')
            ->whereIn('hasil', ['lulus', 'lulus_revisi'])
            ->orderBy('tanggal', 'desc');

        $data = $query->get();

        $headers = [
            'Content-Type' => 'text/csv; charset=UTF-8',
            'Content-Disposition' => 'attachment; filename="Rekap_SK_Yudisium.csv"',
        ];

        $callback = function () use ($data) {
            $file = fopen('php://output', 'w');
            fprintf($file, chr(0xEF) . chr(0xBB) . chr(0xBF));

            fputcsv($file, [
                'No',
                'NIM',
                'Nama Mahasiswa',
                'Program Studi',
                'Judul Skripsi',
                'Tanggal Ujian',
                'Nilai',
                'Nomor SK',
                'Tanggal SK',
                'IPK',
                'Predikat',
                'Status',
            ]);

            $no = 1;
            foreach ($data as $item) {
                $sk = $item->skripsi?->skYudisium;
                $predikat = $sk?->predikat;
                if ($predikat) {
                    $predikat = ucwords(str_replace('_', ' ', $predikat));
                }

                fputcsv($file, [
                    $no++,
                    $item->skripsi->mahasiswa->nim ?? '-',
                    $item->skripsi->mahasiswa->nama ?? '-',
                    $item->skripsi->mahasiswa->prodi->nama ?? '-',
                    $item->skripsi->judul ?? '-',
                    $item->tanggal ? Carbon::parse($item->tanggal)->format('d/m/Y') : '-',
                    $item->nilai ?? '-',
                    $sk?->nomor_sk ?? '-',
                    $sk?->tanggal_terbit ? Carbon::parse($sk->tanggal_terbit)->format('d/m/Y') : '-',
                    $sk?->ipk_akhir ?? '-',
                    $predikat ?? '-',
                    $sk ? 'SK Terbit' : 'Siap Yudisium',
                ]);
            }

            fclose($file);
        };

        return response()->stream($callback, 200, $headers);
    }
}
