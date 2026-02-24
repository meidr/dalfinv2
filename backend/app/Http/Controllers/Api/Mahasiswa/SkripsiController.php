<?php

namespace App\Http\Controllers\Api\Mahasiswa;

use App\Http\Controllers\Controller;
use App\Models\Skripsi;
use App\Models\Bimbingan;
use App\Models\Dokumen;
use App\Models\NotaBimbingan;
use App\Models\SKTugas;
use App\Models\SKYudisium;
use App\Models\Seminar;
use App\Models\BeritaAcaraSeminar;
use App\Models\Configuration;
use App\Models\Notification;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Barryvdh\DomPDF\Facade\Pdf;

class SkripsiController extends Controller
{
    /**
     * Get mahasiswa's skripsi (active and history)
     */
    public function index(Request $request)
    {
        $mahasiswa = $request->user()->mahasiswa;

        $skripsiList = Skripsi::with(['pembimbing.dosen'])
            ->where('mahasiswa_id', $mahasiswa->id)
            ->orderBy('created_at', 'desc')
            ->get();

        return response()->json([
            'success' => true,
            'data' => $skripsiList
        ]);
    }

    /**
     * Create new skripsi proposal
     */
    public function store(Request $request)
    {
        $request->validate([
            'judul' => 'required|string|max:500',
            'abstrak' => 'nullable|string',
            'kata_kunci' => 'nullable|string',
        ]);

        $mahasiswa = $request->user()->mahasiswa;

        // Check if already has active skripsi
        $activeSkripsi = $mahasiswa->activeSkripsi;
        if ($activeSkripsi) {
            return response()->json([
                'success' => false,
                'message' => 'Anda sudah memiliki skripsi aktif'
            ], 422);
        }

        $skripsi = Skripsi::create([
            'mahasiswa_id' => $mahasiswa->id,
            'judul' => $request->judul,
            'abstrak' => $request->abstrak,
            'kata_kunci' => $request->kata_kunci,
            'status' => 'pengajuan',
            'tanggal_daftar' => now(),
            'is_active' => true,
        ]);

        // Send notification to admins
        Notification::broadcast(
            'pengajuan_skripsi',
            'Pengajuan Skripsi Baru',
            $mahasiswa->nama . ' mengajukan judul skripsi: "' . \Illuminate\Support\Str::limit($request->judul, 80) . '"',
            ['skripsi_id' => $skripsi->id, 'mahasiswa_id' => $mahasiswa->id]
        );

        return response()->json([
            'success' => true,
            'message' => 'Pengajuan skripsi berhasil dibuat',
            'data' => $skripsi
        ], 201);
    }

    /**
     * Get skripsi detail
     */
    public function show(Request $request)
    {
        $mahasiswa = $request->user()->mahasiswa;
        $skripsi = $mahasiswa->activeSkripsi;

        if (!$skripsi) {
            return response()->json([
                'success' => false,
                'message' => 'Skripsi tidak ditemukan'
            ], 404);
        }

        $skripsi->load([
            'pembimbing.dosen',
            'bimbingan.dosen',
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
            'data' => $this->addSeminarGrades($skripsi)
        ]);
    }

    /**
     * Get skripsi detail by ID (for history viewing)
     */
    public function showById(Request $request, $id)
    {
        $mahasiswa = $request->user()->mahasiswa;

        $skripsi = Skripsi::where('id', $id)
            ->where('mahasiswa_id', $mahasiswa->id)
            ->first();

        if (!$skripsi) {
            return response()->json([
                'success' => false,
                'message' => 'Skripsi tidak ditemukan'
            ], 404);
        }

        $skripsi->load([
            'pembimbing.dosen',
            'bimbingan.dosen',
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
            'data' => $this->addSeminarGrades($skripsi)
        ]);
    }

    /**
     * Update skripsi (only allowed in certain statuses)
     */
    public function update(Request $request)
    {
        $mahasiswa = $request->user()->mahasiswa;
        $skripsi = $mahasiswa->activeSkripsi;

        if (!$skripsi) {
            return response()->json([
                'success' => false,
                'message' => 'Skripsi tidak ditemukan'
            ], 404);
        }

        // Only allow updates in draft or revision status
        if (!in_array($skripsi->status, ['draft', 'ditolak'])) {
            return response()->json([
                'success' => false,
                'message' => 'Skripsi tidak dapat diubah pada status ini'
            ], 422);
        }

        $request->validate([
            'judul' => 'sometimes|string|max:500',
            'abstrak' => 'nullable|string',
            'kata_kunci' => 'nullable|string',
        ]);

        $skripsi->fill($request->only(['judul', 'abstrak', 'kata_kunci']));
        if ($skripsi->status === 'ditolak') {
            $skripsi->status = 'pengajuan';
        }
        $skripsi->save();

        return response()->json([
            'success' => true,
            'message' => 'Skripsi berhasil diperbarui',
            'data' => $skripsi
        ]);
    }

    /**
     * Get bimbingan logs
     */
    public function bimbingan(Request $request)
    {
        $mahasiswa = $request->user()->mahasiswa;
        $skripsi = $mahasiswa->activeSkripsi;

        if (!$skripsi) {
            return response()->json([
                'success' => false,
                'message' => 'Skripsi tidak ditemukan'
            ], 404);
        }

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
     * Add new bimbingan log
     */
    public function addBimbingan(Request $request)
    {
        $request->validate([
            'tanggal' => 'required|date',
            'topik' => 'required|string|max:255',
            'deskripsi' => 'nullable|string',
            'dosen_id' => 'required|exists:dosen,id',
            'file_bukti' => 'nullable|file|max:5120',
        ]);

        $mahasiswa = $request->user()->mahasiswa;
        $skripsi = $mahasiswa->activeSkripsi;

        if (!$skripsi) {
            return response()->json([
                'success' => false,
                'message' => 'Skripsi tidak ditemukan'
            ], 404);
        }

        $path = null;
        if ($request->hasFile('file_bukti')) {
            $path = $request->file('file_bukti')->store('bimbingan', 'public');
        }

        $bimbingan = Bimbingan::create([
            'skripsi_id' => $skripsi->id,
            'dosen_id' => $request->dosen_id,
            'tanggal' => $request->tanggal,
            'topik' => $request->topik,
            'deskripsi' => $request->deskripsi,
            'file_bukti' => $path,
            'status' => 'pending',
        ]);

        // Send notification to admins
        Notification::broadcast(
            'tambah_bimbingan',
            'Log Bimbingan Baru',
            $mahasiswa->nama . ' menambahkan log bimbingan: "' . \Illuminate\Support\Str::limit($request->topik, 80) . '"',
            ['skripsi_id' => $skripsi->id, 'mahasiswa_id' => $mahasiswa->id, 'bimbingan_id' => $bimbingan->id]
        );

        return response()->json([
            'success' => true,
            'message' => 'Log bimbingan berhasil ditambahkan',
            'data' => $bimbingan->load('dosen')
        ], 201);
    }

    /**
     * Get dokumen for current mahasiswa's active skripsi
     */
    public function getDokumen(Request $request)
    {
        $mahasiswa = $request->user()->mahasiswa;
        $skripsi = $mahasiswa->activeSkripsi;

        if (!$skripsi) {
            return response()->json([
                'success' => false,
                'message' => 'Skripsi tidak ditemukan'
            ], 404);
        }

        $query = Dokumen::where('skripsi_id', $skripsi->id);

        if ($request->filled('jenis')) {
            $query->where('jenis', $request->jenis);
        }

        $dokumen = $query->orderBy('created_at', 'desc')->get();

        return response()->json([
            'success' => true,
            'data' => $dokumen
        ]);
    }

    /**
     * Upload dokumen skripsi (bab, proposal, etc.)
     */
    public function uploadDokumen(Request $request)
    {
        $request->validate([
            'jenis' => 'required|string|in:proposal,bab1,bab2,bab3,bab4,bab5,full_draft,final,revisi,lainnya',
            'file' => 'required|file|max:10240|mimes:pdf,doc,docx',
            'catatan' => 'nullable|string|max:500',
        ]);

        $mahasiswa = $request->user()->mahasiswa;
        $skripsi = $mahasiswa->activeSkripsi;

        if (!$skripsi) {
            return response()->json([
                'success' => false,
                'message' => 'Skripsi tidak ditemukan'
            ], 404);
        }

        // Restrict proposal upload: only allowed when status is pengajuan or proposal
        if ($request->jenis === 'proposal' && !in_array($skripsi->status, ['pengajuan', 'proposal'])) {
            return response()->json([
                'success' => false,
                'message' => 'Upload dokumen proposal hanya diperbolehkan saat status Pengajuan atau Proposal'
            ], 422);
        }

        $file = $request->file('file');
        $path = $file->store('dokumen/' . $skripsi->id, 'public');

        // Get version number
        $existingCount = Dokumen::where('skripsi_id', $skripsi->id)
            ->where('jenis', $request->jenis)
            ->count();

        $jenisLabels = [
            'proposal' => 'Proposal',
            'bab1' => 'Bab 1 - Pendahuluan',
            'bab2' => 'Bab 2 - Tinjauan Pustaka',
            'bab3' => 'Bab 3 - Metodologi Penelitian',
            'bab4' => 'Bab 4 - Hasil dan Pembahasan',
            'bab5' => 'Bab 5 - Kesimpulan dan Saran',
            'full_draft' => 'Draft Lengkap',
            'final' => 'Naskah Final',
            'revisi' => 'Revisi',
            'lainnya' => 'Dokumen Lainnya',
        ];

        $dokumen = Dokumen::create([
            'skripsi_id' => $skripsi->id,
            'jenis' => $request->jenis,
            'nama_file' => $jenisLabels[$request->jenis] ?? $request->jenis,
            'path' => $path,
            'ukuran' => $file->getSize(),
            'versi' => $existingCount + 1,
            'status' => 'pending',
            'catatan' => $request->catatan,
            'uploaded_by' => $request->user()->id,
        ]);

        // Send notification to admins
        $jenisLabel = $jenisLabels[$request->jenis] ?? $request->jenis;
        Notification::broadcast(
            'upload_dokumen',
            'Dokumen Baru Diunggah',
            $mahasiswa->nama . ' mengunggah dokumen: ' . $jenisLabel,
            ['skripsi_id' => $skripsi->id, 'mahasiswa_id' => $mahasiswa->id, 'dokumen_id' => $dokumen->id]
        );

        // Auto-advance skripsi status when proposal is uploaded
        if ($request->jenis === 'proposal' && $skripsi->status === 'pengajuan') {
            $skripsi->status = 'proposal';
            $skripsi->progress_percentage = 15;
            $skripsi->save();
        }

        return response()->json([
            'success' => true,
            'message' => 'Dokumen berhasil diunggah',
            'data' => $dokumen
        ], 201);
    }

    /**
     * Delete uploaded dokumen
     */
    public function deleteDokumen(Request $request, Dokumen $dokumen)
    {
        $mahasiswa = $request->user()->mahasiswa;
        $skripsi = $mahasiswa->activeSkripsi;

        if (!$skripsi || $dokumen->skripsi_id !== $skripsi->id) {
            return response()->json([
                'success' => false,
                'message' => 'Dokumen tidak ditemukan'
            ], 404);
        }

        // Only allow deletion of pending documents
        if ($dokumen->status !== 'pending') {
            return response()->json([
                'success' => false,
                'message' => 'Dokumen yang sudah diverifikasi tidak dapat dihapus'
            ], 422);
        }

        if ($dokumen->path) {
            Storage::disk('public')->delete($dokumen->path);
        }

        $jenisDeleted = $dokumen->jenis;
        $dokumen->delete();

        // Auto-revert: if proposal was deleted and status is 'proposal', revert to 'pengajuan'
        if ($jenisDeleted === 'proposal' && $skripsi->status === 'proposal') {
            // Check if there are still other proposal documents
            $remainingProposal = Dokumen::where('skripsi_id', $skripsi->id)
                ->where('jenis', 'proposal')
                ->exists();
            if (!$remainingProposal) {
                $skripsi->status = 'pengajuan';
                $skripsi->progress_percentage = 5;
                $skripsi->save();
            }
        }

        return response()->json([
            'success' => true,
            'message' => 'Dokumen berhasil dihapus'
        ]);
    }

    /**
     * Check if mahasiswa is eligible to request ujian skripsi
     */
    public function checkUjianEligibility(Request $request)
    {
        $mahasiswa = $request->user()->mahasiswa;
        $skripsi = $mahasiswa->activeSkripsi;

        if (!$skripsi) {
            return response()->json([
                'success' => false,
                'message' => 'Skripsi tidak ditemukan'
            ], 404);
        }

        $skripsi->load(['pembimbing.dosen', 'dokumen']);

        // Get bimbingan requirements from config
        $config = Configuration::where('key', 'syarat_bimbingan_ujian')->first();
        $requirements = $config ? $config->value : ['pembimbing_1' => 8, 'pembimbing_2' => 4];

        // Get pembimbing
        $pembimbing1 = $skripsi->pembimbing->where('jenis', 'pembimbing_1')->first();
        $pembimbing2 = $skripsi->pembimbing->where('jenis', 'pembimbing_2')->first();

        // Count approved bimbingan per pembimbing
        $countPembimbing1 = 0;
        $countPembimbing2 = 0;

        if ($pembimbing1) {
            $countPembimbing1 = Bimbingan::where('skripsi_id', $skripsi->id)
                ->where('dosen_id', $pembimbing1->dosen_id)
                ->where('status', 'approved')
                ->count();
        }

        if ($pembimbing2) {
            $countPembimbing2 = Bimbingan::where('skripsi_id', $skripsi->id)
                ->where('dosen_id', $pembimbing2->dosen_id)
                ->where('status', 'approved')
                ->count();
        }

        // Check naskah final - any uploaded naskah final counts (no approval needed)
        $naskahFinalDoc = $skripsi->dokumen
            ->where('jenis', 'final')
            ->first();
        $hasNaskahFinal = (bool) $naskahFinalDoc;

        // Check status
        $isCorrectStatus = in_array($skripsi->status, ['bimbingan', 'pengajuan_sidang_tolak']);

        // Already requested?
        $alreadyRequested = $skripsi->status === 'pengajuan_sidang';

        $bimbinganMet = $countPembimbing1 >= $requirements['pembimbing_1']
            && $countPembimbing2 >= $requirements['pembimbing_2'];

        $eligible = $bimbinganMet && $hasNaskahFinal && $isCorrectStatus;

        return response()->json([
            'success' => true,
            'data' => [
                'eligible' => $eligible,
                'already_requested' => $alreadyRequested,
                'is_correct_status' => $isCorrectStatus,
                'bimbingan' => [
                    'pembimbing_1' => [
                        'dosen' => $pembimbing1 ? $pembimbing1->dosen->nama : null,
                        'count' => $countPembimbing1,
                        'required' => (int) $requirements['pembimbing_1'],
                        'met' => $countPembimbing1 >= $requirements['pembimbing_1'],
                    ],
                    'pembimbing_2' => [
                        'dosen' => $pembimbing2 ? $pembimbing2->dosen->nama : null,
                        'count' => $countPembimbing2,
                        'required' => (int) $requirements['pembimbing_2'],
                        'met' => $countPembimbing2 >= $requirements['pembimbing_2'],
                    ],
                ],
                'naskah_final' => [
                    'uploaded' => $hasNaskahFinal,
                    'status' => $naskahFinalDoc ? $naskahFinalDoc->status : null,
                    'id' => $naskahFinalDoc ? $naskahFinalDoc->id : null,
                ],
            ]
        ]);
    }

    /**
     * Request ujian skripsi (submit pengajuan)
     */
    public function requestUjian(Request $request)
    {
        $mahasiswa = $request->user()->mahasiswa;
        $skripsi = $mahasiswa->activeSkripsi;

        if (!$skripsi) {
            return response()->json([
                'success' => false,
                'message' => 'Skripsi tidak ditemukan'
            ], 404);
        }

        if (!in_array($skripsi->status, ['bimbingan', 'pengajuan_sidang_tolak'])) {
            return response()->json([
                'success' => false,
                'message' => 'Pengajuan ujian hanya bisa dilakukan pada status bimbingan atau setelah penolakan'
            ], 422);
        }

        $skripsi->load(['pembimbing.dosen', 'dokumen']);

        // Re-validate eligibility
        $config = Configuration::where('key', 'syarat_bimbingan_ujian')->first();
        $requirements = $config ? $config->value : ['pembimbing_1' => 8, 'pembimbing_2' => 4];

        $pembimbing1 = $skripsi->pembimbing->where('jenis', 'pembimbing_1')->first();
        $pembimbing2 = $skripsi->pembimbing->where('jenis', 'pembimbing_2')->first();

        if (!$pembimbing1 || !$pembimbing2) {
            return response()->json([
                'success' => false,
                'message' => 'Dosen pembimbing belum lengkap'
            ], 422);
        }

        $countP1 = Bimbingan::where('skripsi_id', $skripsi->id)
            ->where('dosen_id', $pembimbing1->dosen_id)
            ->where('status', 'approved')
            ->count();

        $countP2 = Bimbingan::where('skripsi_id', $skripsi->id)
            ->where('dosen_id', $pembimbing2->dosen_id)
            ->where('status', 'approved')
            ->count();

        if ($countP1 < $requirements['pembimbing_1'] || $countP2 < $requirements['pembimbing_2']) {
            return response()->json([
                'success' => false,
                'message' => 'Jumlah bimbingan belum memenuhi syarat'
            ], 422);
        }

        $hasNaskahFinal = $skripsi->dokumen
            ->where('jenis', 'final')
            ->isNotEmpty();

        if (!$hasNaskahFinal) {
            return response()->json([
                'success' => false,
                'message' => 'Naskah final belum diunggah'
            ], 422);
        }

        // Update status
        $skripsi->status = 'pengajuan_sidang';
        $skripsi->alasan_tolak_sidang = null;
        $skripsi->save();

        // Notify dosen pembimbing utama
        $dosenUtama = $pembimbing1->dosen;
        Notification::create([
            'type' => 'pengajuan_ujian',
            'title' => 'Pengajuan Ujian Skripsi',
            'message' => $mahasiswa->nama . ' mengajukan ujian skripsi dan menunggu persetujuan Anda.',
            'data' => ['skripsi_id' => $skripsi->id, 'mahasiswa_id' => $mahasiswa->id],
            'user_id' => $dosenUtama->user_id ?? null,
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Pengajuan ujian skripsi berhasil dikirim. Menunggu persetujuan dosen pembimbing utama.',
        ]);
    }

    /**
     * Download official PDF
     */
    public function downloadOfficialPdf(Request $request, $type)
    {
        $mahasiswa = $request->user()->mahasiswa;
        $skripsi = $mahasiswa->activeSkripsi;

        if (!$skripsi) {
            return response()->json([
                'success' => false,
                'message' => 'Skripsi tidak ditemukan'
            ], 404);
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
        $skripsi->load(['mahasiswa.prodi.fakultas', 'pembimbing.dosen']);

        $skTugas = $skripsi->skTugas;
        if (!$skTugas) {
            return response()->json([
                'success' => false,
                'message' => 'SK Tugas belum diterbitkan'
            ], 404);
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

    private function downloadSkPenguji(Skripsi $skripsi, string $jenis)
    {
        $label = $jenis === 'sempro' ? 'Seminar Proposal' : ($jenis === 'semhas' ? 'Seminar Hasil' : 'Sidang Skripsi');

        $seminar = $skripsi->seminar()->where('jenis', $jenis)->first();

        if (!$seminar || $seminar->penguji->isEmpty()) {
            return response()->json([
                'success' => false,
                'message' => "SK Penguji {$label} belum tersedia"
            ], 404);
        }

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
            'institution' => "Universitas Islam Internasional Darullughah Wadda'wah",
            'city' => 'Bangil',
            'kaprodi' => $this->resolveKaprodi($prodi),
            'dekan' => $this->resolveDekan($fakultas),
        ];

        $pdf = Pdf::loadView('pdf.sk-penguji', $data);
        $pdf->setPaper('a4', 'portrait');

        $nim = $skripsi->mahasiswa->nim;
        return $pdf->download("SK_Penguji_{$jenis}_{$nim}.pdf");
    }

    private function downloadBeritaAcara(Skripsi $skripsi, string $jenis)
    {
        $label = $jenis === 'sempro' ? 'Seminar Proposal' : ($jenis === 'semhas' ? 'Seminar Hasil' : 'Sidang Skripsi');

        $seminar = $skripsi->seminar()->where('jenis', $jenis)->first();

        if (!$seminar) {
            return response()->json([
                'success' => false,
                'message' => "Berita Acara {$label} belum tersedia"
            ], 404);
        }

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
                'message' => "Berita Acara {$label} belum dibuat"
            ], 404);
        }

        // Find ketua penguji for perbaikan signature
        $ketuaPenguji = $seminar->penguji->firstWhere('peran', 'ketua');

        $data = [
            'seminar' => $seminar,
            'beritaAcara' => $beritaAcara,
            'jenisLabel' => $label,
            'tanggal' => now()->translatedFormat('d F Y'),
            'perbaikan' => $seminar->perbaikanProposal,
            'ketuaPenguji' => $ketuaPenguji,
        ];

        $pdf = Pdf::loadView('pdf.berita-acara-seminar', $data);
        $pdf->setPaper('a4', 'portrait');

        $nim = $skripsi->mahasiswa->nim;
        return $pdf->download("Berita_Acara_{$jenis}_{$nim}.pdf");
    }

    private function downloadSkYudisium(Skripsi $skripsi)
    {
        $skYudisium = $skripsi->skYudisium;

        if (!$skYudisium) {
            return response()->json([
                'success' => false,
                'message' => 'SK Yudisium belum diterbitkan'
            ], 404);
        }

        $skripsi->load(['mahasiswa.prodi', 'pembimbing.dosen', 'nilai']);

        $data = [
            'skripsi' => $skripsi,
            'skYudisium' => $skYudisium,
            'tanggal' => now()->translatedFormat('d F Y'),
        ];

        $pdf = Pdf::loadView('pdf.sk-yudisium', $data);
        $pdf->setPaper('a4', 'portrait');

        $nim = $skripsi->mahasiswa->nim;
        return $pdf->download("SK_Yudisium_{$nim}.pdf");
    }

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
     * Add grade and scoring status to each seminar/ujian
     */
    private function addSeminarGrades($skripsi)
    {
        $data = $skripsi->toArray();

        // Seminar is hasMany (array of items)
        if (!empty($data['seminar'])) {
            foreach ($data['seminar'] as &$sem) {
                $sem['grade'] = !empty($sem['nilai']) ? $this->getGrade($sem['nilai']) : null;
                $pengujiList = $sem['penguji'] ?? [];
                $totalPenguji = count($pengujiList);
                $scoredPenguji = collect($pengujiList)->filter(fn($p) => $p['nilai'] !== null)->count();
                $sem['all_scored'] = $totalPenguji > 0 && $scoredPenguji === $totalPenguji;
                $sem['scored_count'] = $scoredPenguji;
                $sem['total_penguji'] = $totalPenguji;
            }
        }

        // Ujian is hasOne (single item)
        if (!empty($data['ujian'])) {
            $data['ujian']['grade'] = !empty($data['ujian']['nilai']) ? $this->getGrade($data['ujian']['nilai']) : null;
            $pengujiList = $data['ujian']['penguji'] ?? [];
            $totalPenguji = count($pengujiList);
            $scoredPenguji = collect($pengujiList)->filter(fn($p) => $p['nilai'] !== null)->count();
            $data['ujian']['all_scored'] = $totalPenguji > 0 && $scoredPenguji === $totalPenguji;
            $data['ujian']['scored_count'] = $scoredPenguji;
            $data['ujian']['total_penguji'] = $totalPenguji;
        }

        return $data;
    }

    private function getGrade($nilai): string
    {
        if ($nilai >= 85) return 'A';
        if ($nilai >= 80) return 'B+';
        if ($nilai >= 70) return 'B';
        if ($nilai >= 65) return 'C+';
        if ($nilai >= 55) return 'C';
        return 'D';
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
