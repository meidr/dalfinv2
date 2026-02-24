<?php

namespace App\Http\Controllers\Api\Mahasiswa;

use App\Http\Controllers\Controller;
use App\Models\Skripsi;
use App\Models\Seminar;
use App\Models\Ujian;
use Illuminate\Http\Request;

class DashboardController extends Controller
{
    /**
     * Get mahasiswa dashboard data
     */
    public function index(Request $request)
    {
        $mahasiswa = $request->user()->mahasiswa;
        $skripsi = $mahasiswa->activeSkripsi;

        if (!$skripsi) {
            return response()->json([
                'success' => true,
                'data' => [
                    'mahasiswa' => $mahasiswa->load(['prodi', 'tahun']),
                    'skripsi' => null,
                    'message' => 'Anda belum memiliki skripsi aktif'
                ]
            ]);
        }

        $skripsi->load([
            'pembimbing.dosen',
            'bimbingan' => function ($q) {
                $q->orderBy('tanggal', 'desc')->limit(5);
            },
        ]);

        // Get upcoming schedule
        $seminar = Seminar::whereHas('skripsi', function ($q) use ($skripsi) {
            $q->where('id', $skripsi->id);
        })
            ->where('status', 'terjadwal')
            ->orderBy('tanggal', 'asc')
            ->first();

        $ujian = Ujian::where('skripsi_id', $skripsi->id)
            ->where('status', 'terjadwal')
            ->first();

        // Bimbingan stats
        $totalBimbingan = $skripsi->bimbingan()->count();
        $approvedBimbingan = $skripsi->bimbingan()->where('status', 'approved')->count();
        $totalDokumen = $skripsi->dokumen()->count();

        // Get completed sempro seminar with penguji and berita_acara
        $semproSeminar = Seminar::where('skripsi_id', $skripsi->id)
            ->where('jenis', 'sempro')
            ->with(['penguji.dosen', 'beritaAcara'])
            ->orderBy('created_at', 'desc')
            ->first();

        // Get semhas seminar
        $semhasSeminar = Seminar::where('skripsi_id', $skripsi->id)
            ->where('jenis', 'semhas')
            ->with(['penguji.dosen', 'beritaAcara'])
            ->orderBy('created_at', 'desc')
            ->first();

        // Get sidang from seminar table (jenis='sidang')
        $sidangSeminar = Seminar::where('skripsi_id', $skripsi->id)
            ->where('jenis', 'sidang')
            ->with(['penguji.dosen', 'beritaAcara'])
            ->orderBy('created_at', 'desc')
            ->first();

        // Get SK Yudisium
        $skYudisium = $skripsi->skYudisium;

        return response()->json([
            'success' => true,
            'data' => [
                'mahasiswa' => $mahasiswa->load(['prodi', 'tahun']),
                'skripsi' => $skripsi,
                'stats' => [
                    'progress' => $this->calculateProgress($skripsi->status),
                    'total_bimbingan' => $totalBimbingan,
                    'approved_bimbingan' => $approvedBimbingan,
                    'total_dokumen' => $totalDokumen,
                ],
                'upcoming_seminar' => $seminar,
                'upcoming_ujian' => $ujian,
                'sempro_seminar' => $semproSeminar,
                'semhas_seminar' => $semhasSeminar,
                'sidang_seminar' => $sidangSeminar,
                'sk_yudisium' => $skYudisium,
            ]
        ]);
    }

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
