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
    public function index(Request $request)
    {
        $user = $request->user();

        // Staff gender scope: filter skripsi to only matching-gender mahasiswa
        $staffGenderScope = function ($query) use ($user) {
            if ($user->role === 'staff' && $user->jenis_kelamin) {
                $query->whereHas('mahasiswa', function ($q) use ($user) {
                    $q->where('jenis_kelamin', $user->jenis_kelamin);
                });
            }
        };

        $totalSkripsi = Skripsi::where(function ($q) use ($staffGenderScope) {
            $staffGenderScope($q);
        })->count();
        $proposalAktif = Skripsi::whereIn('status', ['draft', 'pengajuan', 'proposal'])
            ->where(function ($q) use ($staffGenderScope) {
                $staffGenderScope($q);
            })
            ->count();
        $menungguSK = Skripsi::where('status', 'disetujui')
            ->doesntHave('skTugas')
            ->where(function ($q) use ($staffGenderScope) {
                $staffGenderScope($q);
            })
            ->count();
        $selesai = Skripsi::where('status', 'lulus')
            ->where(function ($q) use ($staffGenderScope) {
                $staffGenderScope($q);
            })
            ->count();

        // Status distribution
        $statusDistribution = Skripsi::selectRaw('status, count(*) as total')
            ->where(function ($q) use ($staffGenderScope) {
                $staffGenderScope($q);
            })
            ->groupBy('status')
            ->get()
            ->pluck('total', 'status');

        // Perlu diproses - recent submissions
        $perluDiproses = Skripsi::with(['mahasiswa.prodi', 'pembimbing.dosen'])
            ->whereIn('status', ['pengajuan', 'disetujui', 'sempro', 'semhas'])
            ->where(function ($q) use ($staffGenderScope) {
                $staffGenderScope($q);
            })
            ->orderBy('updated_at', 'desc')
            ->limit(10)
            ->get();

        // Recent activities
        $seminarTerbaru = Seminar::with(['skripsi.mahasiswa'])
            ->where('status', 'terjadwal')
            ->when($user->role === 'staff' && $user->jenis_kelamin, function ($q) use ($user) {
                $q->whereHas('skripsi.mahasiswa', function ($mq) use ($user) {
                    $mq->where('jenis_kelamin', $user->jenis_kelamin);
                });
            })
            ->orderBy('tanggal', 'asc')
            ->limit(5)
            ->get();

        $ujianTerbaru = Ujian::with(['skripsi.mahasiswa'])
            ->where('status', 'terjadwal')
            ->when($user->role === 'staff' && $user->jenis_kelamin, function ($q) use ($user) {
                $q->whereHas('skripsi.mahasiswa', function ($mq) use ($user) {
                    $mq->where('jenis_kelamin', $user->jenis_kelamin);
                });
            })
            ->orderBy('tanggal', 'asc')
            ->limit(5)
            ->get();

        return response()->json([
            'success' => true,
            'data' => [
                'total_skripsi' => $totalSkripsi,
                'skripsi_bimbingan' => Skripsi::where('status', 'bimbingan')->where(function ($q) use ($staffGenderScope) {
                    $staffGenderScope($q);
                })->count(),
                'skripsi_lulus' => $selesai,
                'skripsi_proposal' => $proposalAktif,
                'skripsi_sempro' => Skripsi::where('status', 'sempro')->where(function ($q) use ($staffGenderScope) {
                    $staffGenderScope($q);
                })->count(),
                'skripsi_semhas' => Skripsi::where('status', 'semhas')->where(function ($q) use ($staffGenderScope) {
                    $staffGenderScope($q);
                })->count(),
                'skripsi_sidang' => Skripsi::where('status', 'sidang')->where(function ($q) use ($staffGenderScope) {
                    $staffGenderScope($q);
                })->count(),
                'total_mahasiswa' => Mahasiswa::when($user->role === 'staff' && $user->jenis_kelamin, function ($q) use ($user) {
                    $q->where('jenis_kelamin', $user->jenis_kelamin);
                })->count(),
                'total_dosen' => Dosen::count(),
                'status_distribution' => $statusDistribution,
                'perlu_diproses' => $perluDiproses,
                'seminar_terbaru' => $seminarTerbaru,
                'ujian_terbaru' => $ujianTerbaru,
                'recent_activities' => $perluDiproses->take(5)->map(function ($item) {
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
