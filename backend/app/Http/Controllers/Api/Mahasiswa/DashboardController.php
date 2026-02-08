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
                    'mahasiswa' => $mahasiswa->load('prodi'),
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

        return response()->json([
            'success' => true,
            'data' => [
                'mahasiswa' => $mahasiswa->load('prodi'),
                'skripsi' => $skripsi,
                'stats' => [
                    'progress' => $skripsi->progress_percentage,
                    'total_bimbingan' => $totalBimbingan,
                    'approved_bimbingan' => $approvedBimbingan,
                ],
                'upcoming_seminar' => $seminar,
                'upcoming_ujian' => $ujian,
            ]
        ]);
    }
}
