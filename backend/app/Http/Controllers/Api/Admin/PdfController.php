<?php

namespace App\Http\Controllers\Api\Admin;

use App\Http\Controllers\Controller;
use App\Models\Skripsi;
use App\Models\SKTugas;
use App\Models\SKYudisium;
use App\Models\NotaBimbingan;
use App\Models\BeritaAcara;
use App\Models\Seminar;
use App\Models\Prodi;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Request;
use Carbon\Carbon;

class PdfController extends Controller
{
    /**
     * Generate SK Tugas Pembimbing PDF
     */
    public function skTugas(Request $request, Skripsi $skripsi)
    {
        $skripsi->load(['mahasiswa.prodi.fakultas', 'pembimbing.dosen']);

        // Get or create SK Tugas
        $skTugas = $skripsi->skTugas;
        if (!$skTugas) {
            $nomor = 'SK/' . date('Y') . '/' . str_pad($skripsi->id, 4, '0', STR_PAD_LEFT);
            $skTugas = SKTugas::create([
                'skripsi_id' => $skripsi->id,
                'nomor_sk' => $nomor,
                'tanggal_terbit' => now(),
                'file_sk' => null,
            ]);
        }

        // Auto-resolve KAPRODI for this mahasiswa's prodi
        $prodi = $skripsi->mahasiswa->prodi;
        $signer = $this->resolveKaprodi($prodi);

        $data = [
            'skripsi' => $skripsi,
            'skTugas' => $skTugas,
            'tanggal' => now()->translatedFormat('d F Y'),
            'tahun_ajaran' => $this->getTahunAjaran(),
            'prodi_kode' => $prodi->kode ?? '',
            'prodi_nama' => $prodi->nama ?? '',
            'signer' => $signer,
        ];

        $pdf = Pdf::loadView('pdf.sk-tugas', $data);
        $pdf->setPaper('a4', 'portrait');

        // Mark SK Tugas document as approved (Sudah TTD)
        \App\Models\Dokumen::updateOrCreate(
            [
                'skripsi_id' => $skripsi->id,
                'jenis' => 'sk_tugas',
            ],
            [
                'nama_file' => "SK_Tugas_{$skripsi->mahasiswa->nim}.pdf",
                'path' => '',
                'status' => 'approved',
            ]
        );

        // Auto-update skripsi status to bimbingan
        \App\Models\Skripsi::where('id', $skripsi->id)
            ->whereIn('status', ['pengajuan', 'disetujui', 'penentuan_dospem', 'dospem'])
            ->update(['status' => 'bimbingan', 'progress_percentage' => 50]);

        return $pdf->download("SK_Tugas_{$skripsi->mahasiswa->nim}.pdf");
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

        // Find active periode
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

        // Build full name
        $parts = array_filter([
            $dosen->gelar_depan,
            $dosen->nama,
            $dosen->gelar_belakang,
        ]);
        $signer['name'] = implode(' ', $parts) ?: $dosen->nama;
        $signer['nip'] = $dosen->nip ?? '-';

        // Get tanda tangan
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

        // Try getting dekan from fakultas relation
        $dosen = $fakultas->dekan;

        // Fallback: try JabatanPejabat with kode DEKAN
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

        // Build full name
        $parts = array_filter([
            $dosen->gelar_depan,
            $dosen->nama,
            $dosen->gelar_belakang,
        ]);
        $signer['name'] = implode(' ', $parts) ?: $dosen->nama;
        $signer['nip'] = $dosen->nip ?? '-';

        // Get tanda tangan
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
     * Generate Nota Bimbingan PDF
     */
    public function notaBimbingan(Request $request, Skripsi $skripsi)
    {
        $skripsi->load([
            'mahasiswa.prodi',
            'pembimbing.dosen',
            'bimbingan' => function ($q) {
                $q->with('dosen')->orderBy('tanggal', 'asc');
            }
        ]);

        // Get or create Nota Bimbingan
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

        $data = [
            'skripsi' => $skripsi,
            'nota' => $nota,
            'bimbingan' => $skripsi->bimbingan,
            'tanggal' => now()->translatedFormat('d F Y'),
        ];

        $pdf = Pdf::loadView('pdf.nota-bimbingan', $data);
        $pdf->setPaper('a4', 'portrait');

        return $pdf->download("Nota_Bimbingan_{$skripsi->mahasiswa->nim}.pdf");
    }

    /**
     * Generate Berita Acara Seminar PDF
     */
    public function beritaAcaraSeminar(Request $request, Seminar $seminar)
    {
        $seminar->load([
            'skripsi.mahasiswa.prodi.fakultas',
            'skripsi.pembimbing.dosen',
            'penguji.dosen',
            'beritaAcara',
            'perbaikanProposal',
        ]);

        $beritaAcara = $seminar->beritaAcara;
        if (!$beritaAcara) {
            return response()->json([
                'success' => false,
                'message' => 'Berita acara belum dibuat untuk seminar ini'
            ], 404);
        }

        $jenisLabel = $seminar->jenis === 'sempro' ? 'Seminar Proposal' : ($seminar->jenis === 'semhas' ? 'Seminar Hasil' : 'Sidang Skripsi');
        $ketuaPenguji = $seminar->penguji->firstWhere('peran', 'ketua');

        $data = [
            'seminar' => $seminar,
            'beritaAcara' => $beritaAcara,
            'jenisLabel' => $jenisLabel,
            'tanggal' => now()->translatedFormat('d F Y'),
            'perbaikan' => $seminar->perbaikanProposal,
            'ketuaPenguji' => $ketuaPenguji,
        ];

        $pdf = Pdf::loadView('pdf.berita-acara-seminar', $data);
        $pdf->setPaper('a4', 'portrait');

        $nim = $seminar->skripsi->mahasiswa->nim;
        return $pdf->download("Berita_Acara_{$seminar->jenis}_{$nim}.pdf");
    }

    /**
     * Preview SK Tugas (return HTML)
     */
    public function previewSkTugas(Skripsi $skripsi)
    {
        $skripsi->load(['mahasiswa.prodi', 'pembimbing.dosen']);

        $skTugas = $skripsi->skTugas ?? (object)[
            'nomor' => 'SK/' . date('Y') . '/' . str_pad($skripsi->id, 4, '0', STR_PAD_LEFT),
            'tanggal_terbit' => now(),
        ];

        return view('pdf.sk-tugas', [
            'skripsi' => $skripsi,
            'skTugas' => $skTugas,
            'tanggal' => now()->translatedFormat('d F Y'),
            'tahun_ajaran' => $this->getTahunAjaran(),
        ]);
    }

    /**
     * Generate SK Penguji Ujian Skripsi PDF
     */
    public function skPenguji(Request $request, Seminar $seminar)
    {
        $seminar->load([
            'skripsi.mahasiswa.prodi.fakultas',
            'skripsi.pembimbing.dosen',
            'penguji.dosen'
        ]);

        $prodi = $seminar->skripsi->mahasiswa->prodi;
        $fakultas = $prodi->fakultas ?? null;

        $data = [
            'seminar' => $seminar,
            'skripsi' => $seminar->skripsi,
            'tanggal' => now()->translatedFormat('d F Y'),
            'tahun_ajaran' => $this->getTahunAjaran(),
            'prodi_lengkap' => $prodi->nama ?? '',
            'fakultas' => $fakultas->nama_fakultas ?? '-',
            'kaprodi' => $this->resolveKaprodi($prodi),
            'dekan' => $this->resolveDekan($fakultas),
            'city' => 'Bangil',
            'institution' => "Universitas Islam Internasional Darullughah Wadda'wah",
        ];

        $pdf = Pdf::loadView('pdf.sk-penguji', $data);
        $pdf->setPaper('a4', 'portrait');

        $nim = $seminar->skripsi->mahasiswa->nim;
        return $pdf->download("SK_Penguji_{$nim}.pdf");
    }

    /**
     * Generate Jadwal Ujian Skripsi PDF (landscape table)
     */
    public function jadwalUjian(Request $request)
    {
        $query = Seminar::with([
            'skripsi.mahasiswa.prodi',
            'skripsi.pembimbing.dosen',
            'penguji.dosen'
        ])->where('jenis', 'sidang');

        // Apply filters
        if ($request->filled('prodi_id')) {
            $query->whereHas('skripsi.mahasiswa', function ($q) use ($request) {
                $q->where('prodi_id', $request->prodi_id);
            });
        }

        if ($request->filled('tahun_akademik')) {
            $tahun = $request->tahun_akademik;
            if (str_contains($tahun, '/')) {
                $parts = explode('/', $tahun);
                $startYear = (int) $parts[0];
                $endYear = (int) $parts[1];
                $query->whereBetween('tanggal', [
                    "{$startYear}-08-01",
                    "{$endYear}-07-31"
                ]);
            }
        }

        if ($request->filled('semester')) {
            $sem = $request->semester;
            if ($sem === 'ganjil') {
                $query->where(function ($q) {
                    $q->whereMonth('tanggal', '>=', 8)
                        ->orWhereMonth('tanggal', '<=', 1);
                });
            } elseif ($sem === 'genap') {
                $query->whereMonth('tanggal', '>=', 2)
                    ->whereMonth('tanggal', '<=', 7);
            }
        }

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        $ujianList = $query->orderBy('tanggal', 'asc')->orderBy('waktu', 'asc')->get();

        // Determine semester label
        $tahunAkademik = $request->input('tahun_akademik', $this->getTahunAjaran());
        $semesterLabel = '';
        if (str_contains($tahunAkademik, 'Ganjil') || str_contains($tahunAkademik, 'Genap')) {
            $semesterLabel = $tahunAkademik;
        } else {
            $semesterLabel = 'Tahun Akademik ' . $tahunAkademik;
        }

        // Get prodi name if filtered
        $prodiName = '';
        $fakultasName = '';
        if ($request->filled('prodi_id')) {
            $prodi = Prodi::find($request->prodi_id);
            if ($prodi) {
                $prodiName = $prodi->nama;
                $fakultasName = $prodi->fakultas ?? '';
            }
        }

        $data = [
            'ujianList' => $ujianList,
            'tanggal' => now()->translatedFormat('d F Y'),
            'tahun_ajaran' => $tahunAkademik,
            'semester_label' => $semesterLabel,
            'prodi_name' => $prodiName,
            'fakultas_name' => $fakultasName,
            'kaprodi' => [
                'name'      => $request->input('kaprodi_name', 'Nama Kaprodi'),
                'nip'       => $request->input('kaprodi_nip', '-'),
                'position'  => $request->input('kaprodi_position', 'Kepala Program Studi'),
                'signature' => $request->input('kaprodi_signature'),
            ],
            'dekan' => [
                'name'      => $request->input('dekan_name', 'Nama Dekan'),
                'nip'       => $request->input('dekan_nip', '-'),
                'position'  => $request->input('dekan_position', 'Dekan Fakultas'),
                'signature' => $request->input('dekan_signature'),
            ],
            'city' => $request->input('city', 'Bangil'),
        ];

        $pdf = Pdf::loadView('pdf.jadwal-ujian', $data);
        $pdf->setPaper('a4', 'landscape');

        return $pdf->download("Jadwal_Ujian_Skripsi.pdf");
    }

    /**
     * Export Rekap SK Yudisium PDF (landscape - like jadwal ujian)
     */
    public function rekapYudisium(Request $request)
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

        $items = $query->get();

        $data = [
            'items' => $items,
            'tanggal' => now()->translatedFormat('d F Y'),
            'tahun_ajaran' => $this->getTahunAjaran(),
            'kaprodi' => [
                'name'      => $request->input('kaprodi_name', 'Nama Kaprodi'),
                'nip'       => $request->input('kaprodi_nip', '-'),
                'position'  => $request->input('kaprodi_position', 'Kepala Program Studi'),
                'signature' => $request->input('kaprodi_signature'),
            ],
            'dekan' => [
                'name'      => $request->input('dekan_name', 'Nama Dekan'),
                'nip'       => $request->input('dekan_nip', '-'),
                'position'  => $request->input('dekan_position', 'Dekan Fakultas'),
                'signature' => $request->input('dekan_signature'),
            ],
            'city' => $request->input('city', 'Bangil'),
        ];

        $pdf = Pdf::loadView('pdf.rekap-yudisium', $data);
        $pdf->setPaper('a4', 'landscape');

        return $pdf->download("Rekap_SK_Yudisium.pdf");
    }

    /**
     * Generate individual SK Yudisium PDF for a specific skripsi
     */
    public function skYudisium(Request $request, Skripsi $skripsi)
    {
        $skripsi->load(['mahasiswa.prodi', 'pembimbing.dosen', 'skYudisium']);

        $skYudisium = $skripsi->skYudisium;
        if (!$skYudisium) {
            return response()->json([
                'success' => false,
                'message' => 'SK Yudisium belum diterbitkan untuk skripsi ini'
            ], 404);
        }

        // Get the ujian seminar for tanggal ujian
        $ujian = Seminar::where('skripsi_id', $skripsi->id)
            ->where('jenis', 'sidang')
            ->where('status', 'selesai')
            ->first();

        $data = [
            'skripsi' => $skripsi,
            'mahasiswa' => $skripsi->mahasiswa,
            'sk_yudisium' => $skYudisium,
            'tanggal_ujian' => $ujian?->tanggal ? Carbon::parse($ujian->tanggal)->translatedFormat('d F Y') : '-',
            'tanggal_yudisium' => $skYudisium->tanggal_yudisium ? Carbon::parse($skYudisium->tanggal_yudisium)->translatedFormat('d F Y') : '-',
            'tanggal' => now()->translatedFormat('d F Y'),
            'kaprodi' => [
                'name'      => $request->input('kaprodi_name', 'Nama Kaprodi'),
                'nip'       => $request->input('kaprodi_nip', '-'),
                'position'  => $request->input('kaprodi_position', 'Kepala Program Studi'),
                'signature' => $request->input('kaprodi_signature'),
            ],
            'dekan' => [
                'name'      => $request->input('dekan_name', 'Nama Dekan'),
                'nip'       => $request->input('dekan_nip', '-'),
                'position'  => $request->input('dekan_position', 'Dekan Fakultas'),
                'signature' => $request->input('dekan_signature'),
            ],
            'city' => $request->input('city', 'Bangil'),
        ];

        $pdf = Pdf::loadView('pdf.sk-yudisium', $data);
        $pdf->setPaper('a4', 'portrait');

        $nim = $skripsi->mahasiswa->nim ?? 'unknown';
        return $pdf->download("SK_Yudisium_{$nim}.pdf");
    }

    /**
     * Get current academic year
     */
    private function getTahunAjaran(): string
    {
        $month = now()->month;
        $year = now()->year;

        if ($month >= 8) {
            return "Ganjil " . $year . "/" . ($year + 1);
        } elseif ($month >= 2) {
            return "Genap " . ($year - 1) . "/" . $year;
        } else {
            return "Ganjil " . ($year - 1) . "/" . $year;
        }
    }
}
