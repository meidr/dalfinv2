<?php

namespace App\Http\Controllers\Api\Dosen;

use App\Http\Controllers\Controller;
use App\Models\Skripsi;
use App\Models\Bimbingan;
use App\Models\Seminar;
use Illuminate\Http\Request;
use Carbon\Carbon;

class DashboardController extends Controller
{
    /**
     * Get dosen dashboard data
     */
    public function index(Request $request)
    {
        $dosen = $request->user()->dosen;

        // === Stats ===
        $activeBimbingan = $dosen->pembimbing()->where('is_active', true)->count();
        $inactiveBimbingan = $dosen->pembimbing()->where('is_active', false)->count();
        $totalBimbingan = $activeBimbingan + $inactiveBimbingan;

        // Pending bimbingan logs (waiting for dosen review)
        $pendingLogs = Bimbingan::where('dosen_id', $dosen->id)
            ->where('status', 'pending')
            ->count();

        // Pending by type breakdown - just return total since bimbingan doesn't have jenis_bimbingan
        $pendingByType = [];

        // === Upcoming seminar (nearest as penguji or pembimbing) ===
        $upcomingSeminar = Seminar::with(['skripsi.mahasiswa'])
            ->where(function ($q) use ($dosen) {
                $q->whereHas('penguji', function ($q2) use ($dosen) {
                    $q2->where('dosen_id', $dosen->id);
                })->orWhereHas('skripsi.pembimbing', function ($q2) use ($dosen) {
                    $q2->where('dosen_id', $dosen->id);
                });
            })
            ->where('status', 'terjadwal')
            ->where('tanggal', '>=', now()->toDateString())
            ->orderBy('tanggal', 'asc')
            ->orderBy('waktu', 'asc')
            ->first();

        // === Recent activities (latest bimbingan logs for this dosen) ===
        $recentActivities = Bimbingan::with(['skripsi.mahasiswa'])
            ->where('dosen_id', $dosen->id)
            ->orderBy('created_at', 'desc')
            ->limit(5)
            ->get()
            ->map(function ($log) {
                return [
                    'id' => $log->id,
                    'mahasiswa_nama' => $log->skripsi?->mahasiswa?->nama ?? '-',
                    'mahasiswa_nim' => $log->skripsi?->mahasiswa?->nim ?? '-',
                    'mahasiswa_prodi' => $log->skripsi?->mahasiswa?->prodi?->nama ?? '-',
                    'aktivitas' => $log->topik ?? 'Bimbingan',
                    'file_name' => $log->file_bukti ? basename($log->file_bukti) : null,
                    'status' => $log->status,
                    'catatan' => $log->catatan_dosen,
                    'created_at' => $log->created_at,
                    'skripsi_id' => $log->skripsi_id,
                ];
            });

        // === Jadwal minggu ini (seminars this week) ===
        $startOfWeek = now()->startOfWeek();
        $endOfWeek = now()->endOfWeek();

        $jadwalMingguIni = Seminar::with(['skripsi.mahasiswa'])
            ->where(function ($q) use ($dosen) {
                $q->whereHas('penguji', function ($q2) use ($dosen) {
                    $q2->where('dosen_id', $dosen->id);
                })->orWhereHas('skripsi.pembimbing', function ($q2) use ($dosen) {
                    $q2->where('dosen_id', $dosen->id);
                });
            })
            ->whereBetween('tanggal', [$startOfWeek->toDateString(), $endOfWeek->toDateString()])
            ->where('status', 'terjadwal')
            ->orderBy('tanggal', 'asc')
            ->orderBy('waktu', 'asc')
            ->limit(5)
            ->get()
            ->map(function ($seminar) {
                return [
                    'id' => $seminar->id,
                    'jenis' => $seminar->jenis,
                    'tanggal' => $seminar->tanggal,
                    'waktu' => $seminar->waktu,
                    'ruangan' => $seminar->ruangan,
                    'status' => $seminar->status,
                    'mahasiswa_nama' => $seminar->skripsi?->mahasiswa?->nama ?? '-',
                    'judul_skripsi' => $seminar->skripsi?->judul ?? '-',
                ];
            });

        return response()->json([
            'success' => true,
            'data' => [
                'dosen' => [
                    'id' => $dosen->id,
                    'nama' => $dosen->nama,
                    'full_name' => $dosen->full_name,
                    'nip' => $dosen->nip,
                    'jenis_kelamin' => $dosen->jenis_kelamin,
                ],
                'stats' => [
                    'total_bimbingan' => $totalBimbingan,
                    'active_count' => $activeBimbingan,
                    'inactive_count' => $inactiveBimbingan,
                    'kuota_bimbingan' => $dosen->kuota_bimbingan,
                    'pending_approvals' => $pendingLogs,
                    'pending_by_type' => $pendingByType,
                ],
                'upcoming_seminar' => $upcomingSeminar ? [
                    'id' => $upcomingSeminar->id,
                    'jenis' => $upcomingSeminar->jenis,
                    'tanggal' => $upcomingSeminar->tanggal,
                    'waktu' => $upcomingSeminar->waktu,
                    'ruangan' => $upcomingSeminar->ruangan,
                    'mahasiswa_nama' => $upcomingSeminar->skripsi?->mahasiswa?->nama ?? '-',
                ] : null,
                'recent_activities' => $recentActivities,
                'jadwal_minggu_ini' => $jadwalMingguIni,
            ]
        ]);
    }
}
