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
use App\Models\Configuration;
use App\Models\DocumentToken;
use App\Services\NomorSuratService;
use App\Traits\GenderFilterable;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Request;
use Carbon\Carbon;
use chillerlan\QRCode\Common\EccLevel;
use chillerlan\QRCode\Output\QRGdImagePNG;
use chillerlan\QRCode\QRCode;
use chillerlan\QRCode\QROptions;

class PdfController extends Controller
{
    use GenderFilterable;

    public function __construct(private ?NomorSuratService $nomorSuratService = null)
    {
        $this->nomorSuratService ??= app(NomorSuratService::class);
    }

    /**
     * Generate SK Tugas Pembimbing PDF
     */
    public function skTugas(Request $request, Skripsi $skripsi)
    {
        $skripsi->load(['mahasiswa.prodi.fakultas', 'pembimbing.dosen']);

        // Get or create SK Tugas
        $skTugas = $skripsi->skTugas;
        if (!$skTugas) {
            $nomor = $this->nomorSuratService->generateForSkripsi('sk_tugas', $skripsi);
            $skTugas = SKTugas::create([
                'skripsi_id' => $skripsi->id,
                'nomor_sk' => $nomor,
                'tanggal_terbit' => now(),
                'file_sk' => null,
            ]);
        }
        $this->nomorSuratService->ensureSkTugasNumber($skTugas, $skripsi, $skTugas->tanggal_terbit);

        // Auto-resolve KAPRODI for this mahasiswa's prodi
        $prodi = $skripsi->mahasiswa->prodi;
        $signer = $this->resolveKaprodi($prodi);

        // QR Signature
        $signatureMode = $this->getSignatureMode($request);
        $qrData = null;
        if ($signatureMode === 'qr') {
            $qrData = $this->generateQrToken(
                $request,
                'sk_tugas',
                $skripsi->id,
                $skTugas->nomor_sk ?? $skTugas->nomor ?? '-',
                $signer['name'],
                $signer['position'],
                "SK_Tugas_{$skripsi->mahasiswa->nim}.pdf"
            );
        }

        $data = [
            'skripsi' => $skripsi,
            'skTugas' => $skTugas,
            'tanggal' => now()->translatedFormat('d F Y'),
            'tahun_ajaran' => $this->getTahunAjaran(),
            'prodi_kode' => $prodi->kode ?? '',
            'prodi_nama' => $prodi->nama ?? '',
            'signer' => $signer,
            'signatureMode' => $signatureMode,
            'qrData' => $qrData,
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
    private function resolveKaprodi(mixed $prodi)
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
    private function resolveDekan(mixed $fakultas)
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
            $nomor = $this->nomorSuratService->generateForSkripsi('nota_bimbingan', $skripsi);
            $nota = NotaBimbingan::create([
                'skripsi_id' => $skripsi->id,
                'nomor' => $nomor,
                'tanggal_terbit' => now(),
                'total_bimbingan' => $skripsi->bimbingan->count(),
            ]);
        }
        $this->nomorSuratService->ensureNotaBimbinganNumber($nota, $skripsi, $nota->tanggal_terbit);

        // QR Signature
        $signatureMode = $this->getSignatureMode($request);
        $qrData = null;
        if ($signatureMode === 'qr') {
            $qrData = $this->generateQrToken(
                $request,
                'nota_bimbingan',
                $skripsi->id,
                $nota->nomor ?? '-',
                'Pembimbing',
                'Dosen Pembimbing',
                "Nota_Bimbingan_{$skripsi->mahasiswa->nim}.pdf"
            );
        }

        $data = [
            'skripsi' => $skripsi,
            'nota' => $nota,
            'bimbingan' => $skripsi->bimbingan,
            'tanggal' => now()->translatedFormat('d F Y'),
            'signatureMode' => $signatureMode,
            'qrData' => $qrData,
        ];

        $pdf = Pdf::loadView('pdf.nota-bimbingan', $data);
        $pdf->setPaper('a4', 'portrait');

        return $pdf->download("Nota_Bimbingan_{$skripsi->mahasiswa->nim}.pdf");
    }

    /**
     * Generate Surat Mentor Sempro PDF
     */
    public function suratMentorSempro(Request $request, Skripsi $skripsi)
    {
        $skripsi->load(['mahasiswa.prodi.fakultas', 'mentorSempro.dosen']);

        // Auto-resolve KAPRODI for this mahasiswa's prodi
        $prodi = $skripsi->mahasiswa->prodi;
        $signer = $this->resolveKaprodi($prodi);

        $data = [
            'skripsi' => $skripsi,
            'tanggal' => now()->translatedFormat('d F Y'),
            'prodi_kode' => $prodi->kode ?? '',
            'prodi_nama' => $prodi->nama ?? '',
            'signer' => $signer,
        ];

        $pdf = Pdf::loadView('pdf.surat-mentor-sempro', $data);
        $pdf->setPaper('a4', 'portrait');

        return $pdf->download("Surat_Mentor_Sempro_{$skripsi->mahasiswa->nim}.pdf");
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
        $this->nomorSuratService->ensureBeritaAcaraNumber($beritaAcara, $seminar, $beritaAcara->tanggal);

        $jenisLabel = $seminar->jenis === 'sempro' ? 'Seminar Proposal' : ($seminar->jenis === 'semhas' ? 'Seminar Hasil' : 'Sidang Skripsi');
        $ketuaPenguji = $seminar->penguji->firstWhere('peran', 'ketua');

        // QR Signature
        $signatureMode = $this->getSignatureMode($request);
        $qrData = null;
        $nim = $seminar->skripsi->mahasiswa->nim;

        if ($signatureMode === 'qr') {
            // Generate a single QR code for the entire berita acara document
            $ketuaName = $ketuaPenguji?->dosen?->full_name ?? ($ketuaPenguji?->dosen?->nama ?? '-');
            $qrData = $this->generateQrToken(
                $request,
                'berita_acara',
                $seminar->id,
                $beritaAcara->nomor ?? '-',
                $ketuaName,
                'Ketua Penguji',
                "Berita_Acara_{$seminar->jenis}_{$nim}.pdf"
            );
        }

        $data = [
            'seminar' => $seminar,
            'beritaAcara' => $beritaAcara,
            'jenisLabel' => $jenisLabel,
            'tanggal' => now()->translatedFormat('d F Y'),
            'perbaikan' => $seminar->perbaikanProposal,
            'ketuaPenguji' => $ketuaPenguji,
            'signatureMode' => $signatureMode,
            'qrData' => $qrData,
        ];

        $pdf = Pdf::loadView('pdf.berita-acara-seminar', $data);
        $pdf->setPaper('a4', 'portrait');

        return $pdf->download("Berita_Acara_{$seminar->jenis}_{$nim}.pdf");
    }

    /**
     * Preview SK Tugas (return HTML)
     */
    public function previewSkTugas(Skripsi $skripsi)
    {
        $skripsi->load(['mahasiswa.prodi', 'pembimbing.dosen']);

        $skTugas = $skripsi->skTugas ?? (object)[
            'nomor_sk' => 'DRAFT',
            'nomor' => 'DRAFT',
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
        $kaprodi = $this->resolveKaprodi($prodi);
        $dekan = $this->resolveDekan($fakultas);
        $this->nomorSuratService->ensureSeminarSkPengujiNumber($seminar);

        // QR Signature
        $signatureMode = $this->getSignatureMode($request);
        $qrData = null;
        $nim = $seminar->skripsi->mahasiswa->nim;
        if ($signatureMode === 'qr') {
            $qrData = $this->generateQrToken(
                $request,
                'sk_penguji',
                $seminar->id,
                $seminar->nomor_sk_penguji ?? '-',
                $dekan['name'],
                $dekan['position'],
                "SK_Penguji_{$nim}.pdf"
            );
        }

        $data = [
            'seminar' => $seminar,
            'skripsi' => $seminar->skripsi,
            'tanggal' => now()->translatedFormat('d F Y'),
            'tahun_ajaran' => $this->getTahunAjaran(),
            'prodi_lengkap' => $prodi->nama ?? '',
            'fakultas' => $fakultas->nama_fakultas ?? '-',
            'kaprodi' => $kaprodi,
            'dekan' => $dekan,
            'city' => 'Bangil',
            'institution' => "Universitas Islam Internasional Darullughah Wadda'wah",
            'nomor_sk_penguji' => $seminar->nomor_sk_penguji,
            'signatureMode' => $signatureMode,
            'qrData' => $qrData,
        ];

        $pdf = Pdf::loadView('pdf.sk-penguji', $data);
        $pdf->setPaper('a4', 'portrait');

        return $pdf->download("SK_Penguji_{$nim}.pdf");
    }

    /**
     * Generate Jadwal Ujian Skripsi PDF (landscape table)
     */
    public function jadwalUjian(Request $request)
    {
        $query = Seminar::with([
            'skripsi.mahasiswa.prodi.fakultas',
            'skripsi.pembimbing.dosen',
            'penguji.dosen'
        ])->where('jenis', 'sidang');

        $this->applyGenderFilter($query, $request, 'skripsi.mahasiswa');

        // Apply filters
        if ($request->filled('prodi_id')) {
            $query->whereHas('skripsi.mahasiswa', function ($q) use ($request) {
                $q->where('prodi_id', $request->prodi_id);
            });
        }

        // Filter by fakultas
        if ($request->filled('fakultas_id')) {
            $query->whereHas('skripsi.mahasiswa.prodi', function ($q) use ($request) {
                $q->where('fakultas_id', $request->fakultas_id);
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

        // Search by nama/NIM
        if ($request->filled('search')) {
            $search = $request->search;
            $query->whereHas('skripsi.mahasiswa', function ($q) use ($search) {
                $q->where('nama', 'like', "%{$search}%")
                    ->orWhere('nim', 'like', "%{$search}%");
            });
        }

        // Filter by specific date
        if ($request->filled('tanggal')) {
            $query->whereDate('tanggal', $request->tanggal);
        }

        if (!$request->filled('tanggal') && $request->filled('tanggal_mulai')) {
            $query->whereDate('tanggal', '>=', $request->tanggal_mulai);
        }

        if (!$request->filled('tanggal') && $request->filled('tanggal_selesai')) {
            $query->whereDate('tanggal', '<=', $request->tanggal_selesai);
        }

        // Filter by pembimbing name
        if ($request->filled('pembimbing')) {
            $pembimbing = $request->pembimbing;
            $query->whereHas('skripsi.pembimbing.dosen', function ($q) use ($pembimbing) {
                $q->where(function ($sq) use ($pembimbing) {
                    $sq->where('nama', 'like', "%{$pembimbing}%")
                        ->orWhere('gelar_depan', 'like', "%{$pembimbing}%")
                        ->orWhere('gelar_belakang', 'like', "%{$pembimbing}%");
                });
            });
        }

        // Filter by penguji name
        if ($request->filled('penguji')) {
            $penguji = $request->penguji;
            $query->whereHas('penguji.dosen', function ($q) use ($penguji) {
                $q->where(function ($sq) use ($penguji) {
                    $sq->where('nama', 'like', "%{$penguji}%")
                        ->orWhere('gelar_depan', 'like', "%{$penguji}%")
                        ->orWhere('gelar_belakang', 'like', "%{$penguji}%");
                });
            });
        }

        $ujianList = $query->orderBy('tanggal', 'asc')->orderBy('waktu', 'asc')->get();

        // Determine semester & tahun label
        $tahunAkademik = $request->input('tahun_akademik', $this->getTahunAjaran());
        $semesterLabel = '';
        if ($request->filled('semester')) {
            $semLabel = $request->semester === 'ganjil' ? 'Ganjil' : 'Genap';
            $semesterLabel = "Semester {$semLabel} {$tahunAkademik}";
        } else {
            $semesterLabel = $tahunAkademik;
        }

        // Get prodi & fakultas info
        $prodiName = '';
        $fakultasName = '';
        $prodi = null;
        $fakultas = null;

        if ($request->filled('prodi_id')) {
            $prodi = Prodi::with('fakultas')->find($request->prodi_id);
            if ($prodi) {
                $prodiName = $prodi->nama;
                $fakultas = $prodi->fakultas;
                $fakultasName = $fakultas->nama_fakultas ?? '';
            }
        } elseif ($request->filled('fakultas_id')) {
            $fakultas = \App\Models\Fakultas::with('prodi')->find($request->fakultas_id);
            if ($fakultas) {
                $fakultasName = $fakultas->nama_fakultas ?? '';
                // Use first prodi of this fakultas for kaprodi resolution
                $prodi = $fakultas->prodi->first();
            }
        } else {
            // No filter active: title will show "SEMUA FAKULTAS — SEMUA PRODI"
            // But still resolve prodi/fakultas from first item for signature resolution only
            $firstItem = $ujianList->first();
            if ($firstItem) {
                $prodi = $firstItem->skripsi->mahasiswa->prodi ?? null;
                if ($prodi) {
                    $prodi->load('fakultas');
                    $fakultas = $prodi->fakultas;
                }
            }
        }

        // Resolve signers from pejabat data
        $kaprodi = $prodi ? $this->resolveKaprodi($prodi) : [
            'name' => '-',
            'nip' => '-',
            'position' => 'Kepala Program Studi',
            'signature' => null,
        ];
        $dekan = $fakultas ? $this->resolveDekan($fakultas) : [
            'name' => '-',
            'nip' => '-',
            'position' => 'Dekan Fakultas',
            'signature' => null,
        ];

        // Determine signature mode (qr vs biasa)
        $signatureMode = $this->getSignatureMode($request);
        $qrDataKaprodi = null;
        $qrDataDekan = null;

        if ($signatureMode === 'qr') {
            $nomorSurat = 'JU-' . date('Y') . '-' . str_pad($ujianList->count(), 3, '0', STR_PAD_LEFT);
            $qrDataKaprodi = $this->generateQrToken(
                $request,
                'jadwal_ujian',
                null,
                $nomorSurat,
                $kaprodi['name'],
                $kaprodi['position'],
                'Jadwal_Ujian_Skripsi.pdf'
            );
            $qrDataDekan = $this->generateQrToken(
                $request,
                'jadwal_ujian',
                null,
                $nomorSurat,
                $dekan['name'],
                $dekan['position'],
                'Jadwal_Ujian_Skripsi.pdf'
            );
        }

        $data = [
            'ujianList' => $ujianList,
            'tanggal' => now()->translatedFormat('d F Y'),
            'tahun_ajaran' => $tahunAkademik,
            'semester_label' => $semesterLabel,
            'prodi_name' => $prodiName,
            'fakultas_name' => $fakultasName,
            'kaprodi' => $kaprodi,
            'dekan' => $dekan,
            'signature_mode' => $signatureMode,
            'qr_kaprodi' => $qrDataKaprodi,
            'qr_dekan' => $qrDataDekan,
            'city' => $request->input('city', 'Bangil'),
            'kop_path' => public_path('images/kop surat.jpg'),
            'cap_path' => public_path('images/capori.png'),
        ];

        $pdf = Pdf::loadView('pdf.jadwal-ujian', $data);
        $pdf->setPaper('a4', 'landscape');

        return $pdf->download("Jadwal_Ujian_Skripsi.pdf");
    }



    /**
     * Generate individual SK Yudisium PDF for a specific skripsi
     */
    public function skYudisium(Request $request, Skripsi $skripsi)
    {
        $skripsi->load(['mahasiswa.prodi.fakultas', 'pembimbing.dosen', 'skYudisium']);

        $skYudisium = $skripsi->skYudisium;
        if (!$skYudisium) {
            return response()->json([
                'success' => false,
                'message' => 'SK Yudisium belum diterbitkan untuk skripsi ini'
            ], 404);
        }
        $this->nomorSuratService->ensureSkYudisiumNumber($skYudisium, $skripsi, $skYudisium->tanggal_terbit);

        // Get the ujian seminar for tanggal ujian
        $ujian = Seminar::where('skripsi_id', $skripsi->id)
            ->where('jenis', 'sidang')
            ->where('status', 'selesai')
            ->first();

        // Resolve Kaprodi & Dekan dynamically
        $prodi = $skripsi->mahasiswa->prodi;
        $fakultas = $prodi->fakultas ?? null;
        $kaprodi = $this->resolveKaprodi($prodi);
        $dekan = $this->resolveDekan($fakultas);

        // QR Signature
        $signatureMode = $this->getSignatureMode($request);
        $qrData = null;
        $nim = $skripsi->mahasiswa->nim ?? 'unknown';
        if ($signatureMode === 'qr') {
            $qrData = $this->generateQrToken(
                $request,
                'sk_yudisium',
                $skripsi->id,
                $skYudisium->nomor_sk ?? '-',
                $dekan['name'],
                $dekan['position'],
                "SK_Yudisium_{$nim}.pdf"
            );
        }

        $data = [
            'skripsi' => $skripsi,
            'mahasiswa' => $skripsi->mahasiswa,
            'sk_yudisium' => $skYudisium,
            'tanggal_ujian' => $ujian?->tanggal ? Carbon::parse($ujian->tanggal)->translatedFormat('d F Y') : '-',
            'tanggal_yudisium' => $skYudisium->tanggal_yudisium ? Carbon::parse($skYudisium->tanggal_yudisium)->translatedFormat('d F Y') : '-',
            'tanggal' => now()->translatedFormat('d F Y'),
            'kaprodi' => $kaprodi,
            'dekan' => $dekan,
            'city' => $request->input('city', 'Bangil'),
            'signatureMode' => $signatureMode,
            'qrData' => $qrData,
        ];

        $pdf = Pdf::loadView('pdf.sk-yudisium', $data);
        $pdf->setPaper('a4', 'portrait');

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

    /**
     * Get the signature mode from configuration.
     */
    private function getSignatureMode(Request $request): string
    {
        // If forced via request (e.g. from verification controller)
        if ($request->input('_force_qr')) {
            return 'qr';
        }

        $config = Configuration::where('key', 'jenis_ttd')->first();
        return $config?->value['jenis'] ?? 'biasa';
    }

    /**
     * Generate a QR token and QR code image for a document.
     */
    private function generateQrToken(
        Request $request,
        string $docType,
        ?int $docId,
        string $nomorSurat,
        string $signerName,
        string $signerPosition,
        string $fileName
    ): array {
        // Reuse existing token if provided (for re-generation from verify page)
        $existingToken = $request->input('_existing_token');
        if ($existingToken) {
            $docToken = DocumentToken::where('token', $existingToken)->first();
        }

        if (empty($docToken)) {
            $docToken = DocumentToken::generate(
                $docType,
                $docId,
                $nomorSurat,
                $signerName,
                $signerPosition,
                $fileName
            );
        }

        // Build verification URL
        $frontendUrl = rtrim(env('FRONTEND_URL', config('app.frontend_url', 'http://localhost:5173')), '/');
        $verifyUrl = $frontendUrl . '/verify/' . $docToken->token;

        // Generate QR code as base64 PNG
        $options = new QROptions([
            'outputInterface' => QRGdImagePNG::class,
            'eccLevel' => EccLevel::M,
            'scale' => 5,
            'imageBase64' => false,
        ]);

        $qrcode = (new QRCode($options))->render($verifyUrl);
        $qrBase64 = 'data:image/png;base64,' . base64_encode($qrcode);

        return [
            'token' => $docToken->token,
            'verify_url' => $verifyUrl,
            'qr_base64' => $qrBase64,
        ];
    }

    /**
     * Rekap SK Yudisium PDF - filtered export
     */
    public function rekapYudisium(Request $request)
    {
        $query = Seminar::with([
            'skripsi.mahasiswa.prodi.fakultas',
            'skripsi.pembimbing.dosen',
            'skripsi.skYudisium.prodi.fakultas',
            'penguji.dosen',
        ])->where('jenis', 'sidang')
            ->where('status', 'selesai')
            ->whereIn('hasil', ['lulus', 'lulus_revisi']);

        // Filter by tahun akademik
        if ($request->filled('tahun_akademik')) {
            $tahun = $request->tahun_akademik;
            if (str_contains($tahun, '/')) {
                $parts = explode('/', $tahun);
                $startYear = (int) $parts[0];
                $endYear = (int) $parts[1];
                $query->whereBetween('tanggal', ["{$startYear}-08-01", "{$endYear}-07-31"]);
            }
        }

        // Filter by fakultas
        if ($request->filled('fakultas_id')) {
            $query->whereHas('skripsi.mahasiswa.prodi', function ($q) use ($request) {
                $q->where('fakultas_id', $request->fakultas_id);
            });
        }

        // Filter by prodi
        if ($request->filled('prodi_id')) {
            $query->whereHas('skripsi.mahasiswa', function ($q) use ($request) {
                $q->where('prodi_id', $request->prodi_id);
            });
        }

        // Filter by nomor_sk_batch
        $nomorSkBatch = null;
        if ($request->filled('nomor_sk_batch')) {
            $nomorSkBatch = $request->nomor_sk_batch;
            $query->whereHas('skripsi.skYudisium', function ($q) use ($nomorSkBatch) {
                $q->where('nomor_sk_batch', $nomorSkBatch);
            });
        }

        // Filter by specific skripsi (for individual mahasiswa PDF)
        if ($request->filled('skripsi_id')) {
            $query->where('skripsi_id', $request->skripsi_id);
        }

        $items = $query->orderBy('tanggal', 'desc')->get();
        $items->each(function ($item) {
            $skripsi = $item->skripsi;
            $skYudisium = $skripsi?->skYudisium;
            if ($skripsi && $skYudisium) {
                $this->nomorSuratService->ensureSkYudisiumNumber(
                    $skYudisium,
                    $skripsi,
                    $skYudisium->tanggal_terbit
                );
            }
        });

        // Resolve prodi & fakultas names
        $prodiName = '';
        $fakultasName = '';
        $prodi = null;
        $fakultas = null;

        // If filtering by batch, get prodi & fakultas from sk_yudisium record
        if ($nomorSkBatch) {
            $skRecord = SKYudisium::with('prodi.fakultas')
                ->where('nomor_sk_batch', $nomorSkBatch)
                ->first();
            if ($skRecord && $skRecord->prodi) {
                $prodi = $skRecord->prodi;
                $prodiName = $prodi->nama ?? '';
                $prodi->load('fakultas');
                $fakultas = $prodi->fakultas;
                $fakultasName = $fakultas->nama_fakultas ?? '';
            }
        }

        if ($request->filled('prodi_id')) {
            $prodi = Prodi::with('fakultas')->find($request->prodi_id);
            if ($prodi) {
                $prodiName = $prodi->nama;
                if (!$fakultas) {
                    $fakultas = $prodi->fakultas;
                    $fakultasName = $fakultas->nama_fakultas ?? '';
                }
            }
        } elseif ($request->filled('fakultas_id') && !$fakultas) {
            $fakultas = \App\Models\Fakultas::with('prodi')->find($request->fakultas_id);
            if ($fakultas) {
                $fakultasName = $fakultas->nama_fakultas ?? '';
                $prodi = $fakultas->prodi->first();
                if ($prodi) $prodiName = $prodi->nama;
            }
        }

        // Fallback: resolve from first item
        if (!$fakultas || !$prodi) {
            $firstItem = $items->first();
            if ($firstItem) {
                if (!$prodi) {
                    $prodi = $firstItem->skripsi->mahasiswa->prodi ?? null;
                }
                if ($prodi && !$fakultas) {
                    $prodi->load('fakultas');
                    $fakultas = $prodi->fakultas;
                    $fakultasName = $fakultasName ?: ($fakultas->nama_fakultas ?? '');
                }
                if (!$prodiName && $prodi) {
                    $prodiName = $prodi->nama ?? '';
                }
            }
        }

        // Resolve Dekan signature
        $dekan = $this->resolveDekan($fakultas);

        // Signature mode
        $signatureMode = $this->getSignatureMode($request);
        $qrDataDekan = null;

        if ($signatureMode === 'qr' && $dekan) {
            $nomorSurat = $nomorSkBatch
                ?: ($items->first()?->skripsi?->skYudisium?->nomor_sk ?? '-');
            $qrDataDekan = $this->generateQrToken(
                $request,
                'sk_yudisium',
                null,
                $nomorSurat,
                $dekan['name'],
                $dekan['position'],
                'SK_Yudisium.pdf'
            );
        }

        $tahunAkademik = $request->input('tahun_akademik', $this->getTahunAjaran());

        $data = [
            'items' => $items,
            'tahun_ajaran' => $tahunAkademik,
            'nomor_sk_batch' => $nomorSkBatch,
            'fakultas_name' => $fakultasName,
            'prodi_name' => $prodiName,
            'dekan' => $dekan,
            'signature_mode' => $signatureMode,
            'qr_dekan' => $qrDataDekan,
            'city' => 'Bangil',
            'tanggal' => Carbon::now()->translatedFormat('d F Y'),
            'kop_path' => public_path('images/kop surat.jpg'),
            'cap_path' => public_path('images/capori.png'),
        ];

        $pdf = \Barryvdh\DomPDF\Facade\Pdf::loadView('pdf.rekap-yudisium', $data);
        $pdf->setPaper('A4', 'portrait');

        $filename = $nomorSkBatch
            ? 'SK_Yudisium_' . str_replace(['/', '\\'], '_', $nomorSkBatch) . '.pdf'
            : 'SK_Yudisium.pdf';

        return $pdf->download($filename);
    }
}
