<?php

namespace App\Http\Controllers\Api\Dosen;

use App\Http\Controllers\Controller;
use App\Models\Skripsi;
use App\Models\Seminar;
use App\Models\Bimbingan;
use App\Models\NotaBimbingan;
use App\Models\Configuration;
use Illuminate\Http\Request;
use Barryvdh\DomPDF\Facade\Pdf;

class BimbinganController extends Controller
{
    /**
     * List all mahasiswa bimbingan
     */
    public function index(Request $request)
    {
        $dosen = $request->user()->dosen;

        $query = Skripsi::with(['mahasiswa.prodi', 'bimbingan' => function ($q) {
            $q->orderBy('tanggal', 'desc');
        }])
            ->whereHas('pembimbing', function ($q) use ($dosen) {
                $q->where('dosen_id', $dosen->id)->where('is_active', true);
            })
            ->where('is_active', true);

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('judul', 'like', "%{$search}%")
                    ->orWhereHas('mahasiswa', function ($mhs) use ($search) {
                        $mhs->where('nama', 'like', "%{$search}%")
                            ->orWhere('nim', 'like', "%{$search}%");
                    });
            });
        }

        $skripsi = $query->orderBy('updated_at', 'desc')->get();

        return response()->json([
            'success' => true,
            'data' => $skripsi
        ]);
    }

    /**
     * Show detail of specific skripsi
     */
    public function show(Request $request, Skripsi $skripsi)
    {
        $dosen = $request->user()->dosen;

        // Verify dosen is pembimbing OR penguji
        $isPembimbing = $skripsi->pembimbing()->where('dosen_id', $dosen->id)->exists();
        $isPenguji = \App\Models\Penguji::whereHas('seminar', function ($q) use ($skripsi) {
            $q->where('skripsi_id', $skripsi->id);
        })->where('dosen_id', $dosen->id)->exists();

        if (!$isPembimbing && !$isPenguji) {
            return response()->json([
                'success' => false,
                'message' => 'Anda tidak memiliki akses ke skripsi ini'
            ], 403);
        }

        $skripsi->load([
            'mahasiswa.prodi',
            'pembimbing.dosen',
            'bimbingan' => function ($q) {
                $q->orderBy('tanggal', 'desc');
            },
            'seminar.penguji.dosen',
            'seminar.beritaAcara',
            'ujian.penguji.dosen',
            'ujian.beritaAcara',
            'dokumen',
            'nilai',
            'skTugas',
            'notaBimbingan',
            'skYudisium',
            'history'
        ]);

        return response()->json([
            'success' => true,
            'data' => $skripsi
        ]);
    }

    /**
     * Get bimbingan logs for a skripsi
     */
    public function logs(Request $request, Skripsi $skripsi)
    {
        $dosen = $request->user()->dosen;

        $logs = Bimbingan::with('dosen')
            ->where('skripsi_id', $skripsi->id)
            ->orderBy('tanggal', 'desc')
            ->get();

        return response()->json([
            'success' => true,
            'data' => $logs
        ]);
    }

    /**
     * Approve or reject bimbingan log
     */
    public function updateStatus(Request $request, Bimbingan $bimbingan)
    {
        $request->validate([
            'status' => 'required|in:approved,revision,rejected',
            'catatan_dosen' => 'nullable|string',
        ]);

        $dosen = $request->user()->dosen;

        // Verify dosen is pembimbing
        $isPembimbing = $bimbingan->skripsi->pembimbing()
            ->where('dosen_id', $dosen->id)->exists();
        if (!$isPembimbing) {
            return response()->json([
                'success' => false,
                'message' => 'Anda bukan pembimbing skripsi ini'
            ], 403);
        }

        // Prevent mutations on inactive skripsi
        if (!$bimbingan->skripsi->is_active) {
            return response()->json([
                'success' => false,
                'message' => 'Skripsi sudah tidak aktif, tidak dapat mengubah status bimbingan'
            ], 422);
        }

        $bimbingan->status = $request->status;
        $bimbingan->catatan_dosen = $request->catatan_dosen;
        $bimbingan->dosen_id = $dosen->id;
        $bimbingan->save();

        return response()->json([
            'success' => true,
            'message' => 'Status bimbingan berhasil diperbarui',
            'data' => $bimbingan
        ]);
    }

    /**
     * List ujian skripsi requests pending dosen approval
     * Only shows requests where this dosen is pembimbing_1 (utama)
     */
    public function ujianRequests(Request $request)
    {
        $dosen = $request->user()->dosen;

        $skripsiList = Skripsi::with(['mahasiswa.prodi', 'pembimbing.dosen', 'dokumen'])
            ->where('status', 'pengajuan_sidang')
            ->where('is_active', true)
            ->whereHas('pembimbing', function ($q) use ($dosen) {
                $q->where('dosen_id', $dosen->id)
                    ->where('jenis', 'pembimbing_1')
                    ->where('is_active', true);
            })
            ->orderBy('updated_at', 'desc')
            ->get();

        // Add bimbingan counts
        $skripsiList->transform(function ($skripsi) {
            $pembimbing1 = $skripsi->pembimbing->where('jenis', 'pembimbing_1')->first();
            $pembimbing2 = $skripsi->pembimbing->where('jenis', 'pembimbing_2')->first();

            $skripsi->bimbingan_count_p1 = $pembimbing1
                ? Bimbingan::where('skripsi_id', $skripsi->id)
                ->where('dosen_id', $pembimbing1->dosen_id)
                ->where('status', 'approved')->count()
                : 0;

            $skripsi->bimbingan_count_p2 = $pembimbing2
                ? Bimbingan::where('skripsi_id', $skripsi->id)
                ->where('dosen_id', $pembimbing2->dosen_id)
                ->where('status', 'approved')->count()
                : 0;

            $skripsi->has_naskah_final = $skripsi->dokumen
                ->where('jenis', 'final')
                ->isNotEmpty();

            return $skripsi;
        });

        return response()->json([
            'success' => true,
            'data' => $skripsiList
        ]);
    }

    /**
     * Approve or reject ujian skripsi request
     */
    public function respondUjianRequest(Request $request, Skripsi $skripsi)
    {
        $request->validate([
            'action' => 'required|in:approve,reject',
            'alasan' => 'nullable|string|max:500',
        ]);

        $dosen = $request->user()->dosen;

        // Verify this dosen is pembimbing_1
        $isPembimbing1 = $skripsi->pembimbing()
            ->where('dosen_id', $dosen->id)
            ->where('jenis', 'pembimbing_1')
            ->exists();

        if (!$isPembimbing1) {
            return response()->json([
                'success' => false,
                'message' => 'Anda bukan dosen pembimbing utama skripsi ini'
            ], 403);
        }

        if ($skripsi->status !== 'pengajuan_sidang') {
            return response()->json([
                'success' => false,
                'message' => 'Skripsi tidak dalam status pengajuan sidang'
            ], 422);
        }

        $mahasiswa = $skripsi->mahasiswa;

        if ($request->action === 'approve') {
            // Update status to approved
            $skripsi->status = 'pengajuan_sidang_acc';
            $skripsi->alasan_tolak_sidang = null;
            $skripsi->progress_percentage = 65;
            $skripsi->save();

            // Notify staff/admin that this skripsi is ready for scheduling
            \App\Models\Notification::broadcast(
                'ujian_approved_dosen',
                'Pengajuan Ujian Disetujui Dosen',
                'Dosen ' . $dosen->nama . ' menyetujui pengajuan ujian skripsi ' . $mahasiswa->nama . '. Silakan jadwalkan ujian.',
                ['skripsi_id' => $skripsi->id, 'mahasiswa_id' => $mahasiswa->id]
            );

            return response()->json([
                'success' => true,
                'message' => 'Pengajuan ujian skripsi disetujui. Staff akan menjadwalkan ujian.',
            ]);
        } else {
            // Reject: set status to pengajuan_sidang_tolak
            $skripsi->status = 'pengajuan_sidang_tolak';
            $skripsi->alasan_tolak_sidang = $request->alasan;
            $skripsi->save();

            // Notify mahasiswa
            $mahasiswaUser = $mahasiswa->user ?? \App\Models\User::where('id', $mahasiswa->user_id)->first();
            if ($mahasiswaUser) {
                \App\Models\Notification::create([
                    'type' => 'ujian_rejected',
                    'title' => 'Pengajuan Ujian Ditolak',
                    'message' => 'Pengajuan ujian skripsi Anda ditolak oleh dosen pembimbing.' . ($request->alasan ? ' Alasan: ' . $request->alasan : ''),
                    'data' => ['skripsi_id' => $skripsi->id],
                    'user_id' => $mahasiswaUser->id,
                ]);
            }

            return response()->json([
                'success' => true,
                'message' => 'Pengajuan ujian skripsi ditolak.',
            ]);
        }
    }

    /**
     * Get all jadwal (seminar & ujian) for dosen's bimbingan students AND as penguji
     */
    public function jadwal(Request $request)
    {
        $dosen = $request->user()->dosen;

        // Get all skripsi IDs where this dosen is pembimbing
        $skripsiIds = Skripsi::whereHas('pembimbing', function ($q) use ($dosen) {
            $q->where('dosen_id', $dosen->id)->where('is_active', true);
        })->where('is_active', true)->pluck('id');

        // Get seminar IDs where dosen is penguji
        $pengujiSeminarIds = \App\Models\Penguji::where('dosen_id', $dosen->id)
            ->pluck('seminar_id');

        // Combine: seminars where pembimbing OR penguji
        $query = Seminar::with([
            'skripsi.mahasiswa',
            'penguji.dosen',
            'perbaikanProposal',
        ])->where(function ($q) use ($skripsiIds, $pengujiSeminarIds) {
            $q->whereIn('skripsi_id', $skripsiIds)
                ->orWhereIn('id', $pengujiSeminarIds);
        });

        if ($request->filled('jenis')) {
            $query->where('jenis', $request->jenis);
        }

        $seminars = $query->orderBy('tanggal', 'desc')->get();

        // Add role field and own penguji scores
        $seminars->transform(function ($seminar) use ($dosen, $skripsiIds) {
            $isPembimbing = $skripsiIds->contains($seminar->skripsi_id);
            $ownPenguji = $seminar->penguji->firstWhere('dosen_id', $dosen->id);

            $seminar->role = $ownPenguji ? 'penguji' : ($isPembimbing ? 'pembimbing' : 'penguji');
            $seminar->is_penguji = !!$ownPenguji;
            $seminar->own_penguji = $ownPenguji;
            return $seminar;
        });

        return response()->json([
            'success' => true,
            'data' => $seminars,
        ]);
    }

    /**
     * Download official PDF for a skripsi
     */
    public function downloadOfficialPdf(Request $request, Skripsi $skripsi, $type)
    {
        $dosen = $request->user()->dosen;

        // Verify dosen is pembimbing OR penguji (seminar or ujian)
        $isPembimbing = $skripsi->pembimbing()->where('dosen_id', $dosen->id)->exists();
        $isPenguji = \App\Models\Penguji::whereHas('seminar', function ($q) use ($skripsi) {
            $q->where('skripsi_id', $skripsi->id);
        })->where('dosen_id', $dosen->id)->exists();

        // Also check ujian penguji
        if (!$isPenguji) {
            $isPenguji = \App\Models\PengujiUjian::whereHas('ujian', function ($q) use ($skripsi) {
                $q->where('skripsi_id', $skripsi->id);
            })->where('dosen_id', $dosen->id)->exists();
        }

        if (!$isPembimbing && !$isPenguji) {
            return response()->json([
                'success' => false,
                'message' => 'Anda tidak memiliki akses ke skripsi ini'
            ], 403);
        }

        switch ($type) {
            case 'sk-tugas':
                return $this->downloadSkTugas($skripsi);
            case 'nota-bimbingan':
                return $this->downloadNotaBimbingan($skripsi);
            case 'sk-penguji-sempro':
                return $this->downloadSkPenguji($skripsi, 'sempro');
            case 'sk-penguji-semhas':
                return $this->downloadSkPenguji($skripsi, 'semhas');
            case 'berita-acara-sempro':
                return $this->downloadBeritaAcara($skripsi, 'sempro');
            case 'berita-acara-semhas':
                return $this->downloadBeritaAcara($skripsi, 'semhas');
            case 'sk-penguji-sidang':
                return $this->downloadSkPenguji($skripsi, 'sidang');
            case 'berita-acara-sidang':
                return $this->downloadBeritaAcara($skripsi, 'sidang');
            case 'sk-yudisium':
                return $this->downloadSkYudisium($skripsi);
            default:
                return response()->json([
                    'success' => false,
                    'message' => 'Tipe dokumen tidak valid'
                ], 400);
        }
    }

    private function downloadSkTugas(Skripsi $skripsi)
    {
        $skripsi->load(['mahasiswa.prodi', 'pembimbing.dosen']);
        $skTugas = $skripsi->skTugas;
        if (!$skTugas) {
            return response()->json(['success' => false, 'message' => 'SK Tugas belum diterbitkan'], 404);
        }

        $pdf = Pdf::loadView('pdf.sk-tugas', [
            'skripsi' => $skripsi,
            'skTugas' => $skTugas,
            'tanggal' => now()->translatedFormat('d F Y'),
            'tahun_ajaran' => $this->getTahunAjaran(),
        ]);
        $pdf->setPaper('a4', 'portrait');
        return $pdf->download("SK_Tugas_{$skripsi->mahasiswa->nim}.pdf");
    }

    private function downloadNotaBimbingan(Skripsi $skripsi)
    {
        $skripsi->load([
            'mahasiswa.prodi',
            'pembimbing.dosen',
            'bimbingan' => function ($q) {
                $q->with('dosen')->orderBy('tanggal', 'asc');
            }
        ]);

        $nota = $skripsi->notaBimbingan;
        if (!$nota) {
            $nomor = 'NB/' . date('Y') . '/' . str_pad($skripsi->id, 4, '0', STR_PAD_LEFT);
            $nota = NotaBimbingan::create([
                'skripsi_id' => $skripsi->id,
                'nomor' => $nomor,
                'tanggal_terbit' => now(),
                'total_bimbingan' => $skripsi->bimbingan->count(),
            ]);
        }

        $pdf = Pdf::loadView('pdf.nota-bimbingan', [
            'skripsi' => $skripsi,
            'nota' => $nota,
            'bimbingan' => $skripsi->bimbingan,
            'tanggal' => now()->translatedFormat('d F Y'),
        ]);
        $pdf->setPaper('a4', 'portrait');
        return $pdf->download("Nota_Bimbingan_{$skripsi->mahasiswa->nim}.pdf");
    }

    private function downloadSkPenguji(Skripsi $skripsi, string $jenis)
    {
        $label = $jenis === 'sempro' ? 'Seminar Proposal' : ($jenis === 'semhas' ? 'Seminar Hasil' : 'Sidang Skripsi');

        $seminar = $skripsi->seminar()->where('jenis', $jenis)->first();
        if (!$seminar || $seminar->penguji->isEmpty()) {
            return response()->json(['success' => false, 'message' => "SK Penguji {$label} belum tersedia"], 404);
        }

        $seminar->load(['skripsi.mahasiswa.prodi.fakultas', 'skripsi.pembimbing.dosen', 'penguji.dosen']);

        return $this->generateSkPengujiPdf($seminar, $seminar->skripsi, $jenis);
    }

    private function generateSkPengujiPdf($seminar, Skripsi $skripsi, string $jenis)
    {
        $prodi = $skripsi->mahasiswa->prodi;
        $fakultas = $prodi->fakultas ?? null;

        $pdf = Pdf::loadView('pdf.sk-penguji', [
            'seminar' => $seminar,
            'skripsi' => $skripsi,
            'tanggal' => now()->translatedFormat('d F Y'),
            'tahun_ajaran' => $this->getTahunAjaran(),
            'prodi_lengkap' => $prodi->nama ?? '',
            'fakultas' => $fakultas->nama_fakultas ?? '-',
            'institution' => "Universitas Islam Internasional Darullughah Wadda'wah",
            'city' => 'Bangil',
            'kaprodi' => $this->resolveKaprodi($prodi),
            'dekan' => $this->resolveDekan($fakultas),
        ]);
        $pdf->setPaper('a4', 'portrait');
        return $pdf->download("SK_Penguji_{$jenis}_{$skripsi->mahasiswa->nim}.pdf");
    }

    private function downloadBeritaAcara(Skripsi $skripsi, string $jenis)
    {
        $label = $jenis === 'sempro' ? 'Seminar Proposal' : ($jenis === 'semhas' ? 'Seminar Hasil' : 'Sidang Skripsi');

        $seminar = $skripsi->seminar()->where('jenis', $jenis)->first();
        if (!$seminar) {
            return response()->json(['success' => false, 'message' => "Berita Acara {$label} belum tersedia"], 404);
        }

        $seminar->load(['skripsi.mahasiswa.prodi.fakultas', 'skripsi.pembimbing.dosen', 'penguji.dosen', 'beritaAcara', 'perbaikanProposal']);
        $beritaAcara = $seminar->beritaAcara;
        if (!$beritaAcara) {
            return response()->json(['success' => false, 'message' => "Berita Acara {$label} belum dibuat"], 404);
        }

        $ketuaPenguji = $seminar->penguji->firstWhere('peran', 'ketua');

        $pdf = Pdf::loadView('pdf.berita-acara-seminar', [
            'seminar' => $seminar,
            'beritaAcara' => $beritaAcara,
            'jenisLabel' => $label,
            'tanggal' => now()->translatedFormat('d F Y'),
            'perbaikan' => $seminar->perbaikanProposal,
            'ketuaPenguji' => $ketuaPenguji,
        ]);
        $pdf->setPaper('a4', 'portrait');
        return $pdf->download("Berita_Acara_{$jenis}_{$skripsi->mahasiswa->nim}.pdf");
    }

    private function downloadSkYudisium(Skripsi $skripsi)
    {
        $skYudisium = $skripsi->skYudisium;
        if (!$skYudisium) {
            return response()->json(['success' => false, 'message' => 'SK Yudisium belum diterbitkan'], 404);
        }

        $skripsi->load(['mahasiswa.prodi', 'pembimbing.dosen', 'nilai']);

        $pdf = Pdf::loadView('pdf.sk-yudisium', [
            'skripsi' => $skripsi,
            'skYudisium' => $skYudisium,
            'tanggal' => now()->translatedFormat('d F Y'),
        ]);
        $pdf->setPaper('a4', 'portrait');
        return $pdf->download("SK_Yudisium_{$skripsi->mahasiswa->nim}.pdf");
    }

    private function getTahunAjaran(): string
    {
        $now = now();
        $year = $now->year;
        $month = $now->month;
        if ($month >= 9) {
            return $year . '/' . ($year + 1) . ' Ganjil';
        } elseif ($month >= 2) {
            return ($year - 1) . '/' . $year . ' Genap';
        }
        return ($year - 1) . '/' . $year . ' Ganjil';
    }

    /**
     * Resolve the active KAPRODI for a given prodi
     */
    private function resolveKaprodi($prodi)
    {
        $jabatan = \App\Models\MasterJabatan::where('kode', 'KAPRODI')->first();

        $signer = [
            'name' => '-',
            'nip' => '-',
            'position' => 'Kepala Program Studi ' . ($prodi->nama ?? ''),
            'signature' => null,
        ];

        if (!$jabatan || !$prodi) return $signer;

        $periode = \App\Models\PeriodeJabatan::where('is_active', true)->first();
        if (!$periode) return $signer;

        $today = now()->toDateString();
        $pejabat = \App\Models\JabatanPejabat::with('dosen')
            ->where('periode_id', $periode->id)
            ->where('jabatan_id', $jabatan->id)
            ->where('prodi_id', $prodi->id)
            ->where(function ($q) use ($today) {
                $q->whereNull('tgl_selesai')
                    ->orWhere('tgl_selesai', '>=', $today);
            })
            ->first();

        if (!$pejabat || !$pejabat->dosen) return $signer;

        $dosen = $pejabat->dosen;
        $parts = array_filter([$dosen->gelar_depan, $dosen->nama, $dosen->gelar_belakang]);
        $signer['name'] = implode(' ', $parts) ?: $dosen->nama;
        $signer['nip'] = $dosen->nip ?? '-';

        $ttd = \App\Models\TandaTangan::where('dosen_id', $dosen->id)->first();
        if ($ttd && $ttd->ttd) {
            $path = storage_path('app/public/' . $ttd->ttd);
            if (file_exists($path)) {
                $signer['signature'] = $path;
            }
        }

        return $signer;
    }

    /**
     * Resolve the active DEKAN for a given fakultas
     */
    private function resolveDekan($fakultas)
    {
        $signer = [
            'name' => '-',
            'nip' => '-',
            'position' => 'Dekan ' . ($fakultas->nama_fakultas ?? 'Fakultas'),
            'signature' => null,
        ];

        if (!$fakultas) return $signer;

        $dosen = $fakultas->dekan;

        if (!$dosen) {
            $jabatan = \App\Models\MasterJabatan::where('kode', 'DEKAN')->first();
            if ($jabatan) {
                $periode = \App\Models\PeriodeJabatan::where('is_active', true)->first();
                if ($periode) {
                    $today = now()->toDateString();
                    $pejabat = \App\Models\JabatanPejabat::with('dosen')
                        ->where('periode_id', $periode->id)
                        ->where('jabatan_id', $jabatan->id)
                        ->where('fakultas_id', $fakultas->id)
                        ->where(function ($q) use ($today) {
                            $q->whereNull('tgl_selesai')
                                ->orWhere('tgl_selesai', '>=', $today);
                        })
                        ->first();
                    $dosen = $pejabat?->dosen;
                }
            }
        }

        if (!$dosen) return $signer;

        $parts = array_filter([$dosen->gelar_depan, $dosen->nama, $dosen->gelar_belakang]);
        $signer['name'] = implode(' ', $parts) ?: $dosen->nama;
        $signer['nip'] = $dosen->nip ?? '-';

        $ttd = \App\Models\TandaTangan::where('dosen_id', $dosen->id)->first();
        if ($ttd && $ttd->ttd) {
            $path = storage_path('app/public/' . $ttd->ttd);
            if (file_exists($path)) {
                $signer['signature'] = $path;
            }
        }

        return $signer;
    }
}
