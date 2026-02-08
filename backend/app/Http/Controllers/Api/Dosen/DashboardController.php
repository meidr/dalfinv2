<?php

namespace App\Http\Controllers\Api\Dosen;

use App\Http\Controllers\Controller;
use App\Models\Skripsi;
use App\Models\Bimbingan;
use App\Models\Seminar;
use App\Models\Ujian;
use Illuminate\Http\Request;

class DashboardController extends Controller
{
    /**
     * Get dosen dashboard data
     */
    public function index(Request $request)
    {
        $dosen = $request->user()->dosen;

        // Get mahasiswa bimbingan count
        $totalBimbingan = $dosen->pembimbing()->where('is_active', true)->count();

        // Get pending bimbingan logs
        $pendingLogs = Bimbingan::where('dosen_id', $dosen->id)
            ->where('status', 'pending')
            ->count();

        // Get upcoming seminars
        $upcomingSeminars = Seminar::whereHas('penguji', function ($q) use ($dosen) {
                $q->where('dosen_id', $dosen->id);
            })
            ->orWhereHas('skripsi.pembimbing', function ($q) use ($dosen) {
                $q->where('dosen_id', $dosen->id);
            })
            ->where('status', 'terjadwal')
            ->where('tanggal', '>=', now())
            ->orderBy('tanggal', 'asc')
            ->limit(5)
            ->get();

        // Get upcoming ujian
        $upcomingUjian = Ujian::whereHas('penguji', function ($q) use ($dosen) {
                $q->where('dosen_id', $dosen->id);
            })
            ->orWhereHas('skripsi.pembimbing', function ($q) use ($dosen) {
                $q->where('dosen_id', $dosen->id);
            })
            ->where('status', 'terjadwal')
            ->where('tanggal', '>=', now())
            ->orderBy('tanggal', 'asc')
            ->limit(5)
            ->get();

        // Get recent mahasiswa updates
        $recentUpdates = Skripsi::with(['mahasiswa', 'bimbingan' => function ($q) {
                $q->orderBy('created_at', 'desc')->limit(1);
            }])
            ->whereHas('pembimbing', function ($q) use ($dosen) {
                $q->where('dosen_id', $dosen->id)->where('is_active', true);
            })
            ->where('is_active', true)
            ->orderBy('updated_at', 'desc')
            ->limit(5)
            ->get();

        return response()->json([
            'success' => true,
            'data' => [
                'dosen' => $dosen,
                'stats' => [
                    'total_bimbingan' => $totalBimbingan,
                    'kuota_bimbingan' => $dosen->kuota_bimbingan,
                    'pending_logs' => $pendingLogs,
                ],
                'upcoming_seminars' => $upcomingSeminars,
                'upcoming_ujian' => $upcomingUjian,
                'recent_updates' => $recentUpdates,
            ]
        ]);
    }
}
