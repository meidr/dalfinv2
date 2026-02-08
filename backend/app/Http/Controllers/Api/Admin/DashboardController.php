<?php

namespace App\Http\Controllers\Api\Admin;

use App\Http\Controllers\Controller;
use App\Models\Skripsi;
use App\Models\Mahasiswa;
use App\Models\Dosen;
use App\Models\Seminar;
use App\Models\Ujian;
use Carbon\Carbon;
use Illuminate\Http\Request;

class DashboardController extends Controller
{
    /**
     * Get dashboard statistics
     */
    public function index()
    {
        $totalSkripsi = Skripsi::where('is_active', true)->count();
        $proposalAktif = Skripsi::where('is_active', true)
            ->whereIn('status', ['draft', 'pengajuan', 'proposal'])
            ->count();
        $menungguSK = Skripsi::where('is_active', true)
            ->where('status', 'disetujui')
            ->doesntHave('skTugas')
            ->count();
        $selesai = Skripsi::where('status', 'lulus')->count();

        // Status distribution
        $statusDistribution = Skripsi::where('is_active', true)
            ->selectRaw('status, count(*) as total')
            ->groupBy('status')
            ->get()
            ->pluck('total', 'status');

        // Perlu diproses - recent submissions
        $perluDiproses = Skripsi::with(['mahasiswa.prodi', 'pembimbing.dosen'])
            ->where('is_active', true)
            ->whereIn('status', ['pengajuan', 'disetujui', 'sempro', 'semhas'])
            ->orderBy('updated_at', 'desc')
            ->limit(10)
            ->get();

        // Recent activities
        $seminarTerbaru = Seminar::with(['skripsi.mahasiswa'])
            ->where('status', 'terjadwal')
            ->orderBy('tanggal', 'asc')
            ->limit(5)
            ->get();

        $ujianTerbaru = Ujian::with(['skripsi.mahasiswa'])
            ->where('status', 'terjadwal')
            ->orderBy('tanggal', 'asc')
            ->limit(5)
            ->get();

        // Get counts by status for the chart
        $skripsiByStatus = Skripsi::selectRaw('status, count(*) as total')
            ->groupBy('status')
            ->pluck('total', 'status');

        return response()->json([
            'success' => true,
            'data' => [
                'total_skripsi' => $totalSkripsi,
                'skripsi_bimbingan' => Skripsi::where('status', 'bimbingan')->count(),
                'skripsi_lulus' => $selesai,
                'skripsi_proposal' => $proposalAktif,
                'skripsi_sempro' => Skripsi::where('status', 'sempro')->count(),
                'skripsi_semhas' => Skripsi::where('status', 'semhas')->count(),
                'skripsi_ujian' => Skripsi::where('status', 'ujian')->count(),
                'total_mahasiswa' => Mahasiswa::count(),
                'total_dosen' => Dosen::count(),
                'status_distribution' => $statusDistribution,
                'perlu_diproses' => $perluDiproses,
                'seminar_terbaru' => $seminarTerbaru,
                'ujian_terbaru' => $ujianTerbaru,
                'recent_activities' => $perluDiproses->take(5)->map(function($item) {
                    return [
                        'id' => $item->id,
                        'mahasiswa' => [
                            'nama' => $item->mahasiswa->nama ?? 'Unknown',
                            'nim' => $item->mahasiswa->nim ?? '-',
                        ],
                        'judul' => $item->judul,
                        'status' => $item->status,
                        'updated_at' => $item->updated_at,
                    ];
                }),
            ]
        ]);
    }
}
