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

        $dokumen->delete();

        return response()->json([
            'success' => true,
            'message' => 'Dokumen berhasil dihapus'
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
        $skripsi->load(['mahasiswa.prodi', 'pembimbing.dosen']);

        $skTugas = $skripsi->skTugas;
        if (!$skTugas) {
            return response()->json([
                'success' => false,
                'message' => 'SK Tugas belum diterbitkan'
            ], 404);
        }

        $data = [
            'skripsi' => $skripsi,
            'skTugas' => $skTugas,
            'tanggal' => now()->translatedFormat('d F Y'),
            'tahun_ajaran' => $this->getTahunAjaran(),
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
            'skripsi.mahasiswa.prodi',
            'skripsi.pembimbing.dosen',
            'penguji.dosen'
        ]);

        $config = Configuration::where('key', 'sk_tugas_signer')->first();
        $signerData = $config ? (is_array($config->value) ? $config->value : json_decode($config->value, true)) : [];

        $data = [
            'seminar' => $seminar,
            'skripsi' => $seminar->skripsi,
            'tanggal' => now()->translatedFormat('d F Y'),
            'tahun_ajaran' => $this->getTahunAjaran(),
            'prodi_lengkap' => $seminar->skripsi->mahasiswa->prodi->nama ?? '',
            'fakultas' => $seminar->skripsi->mahasiswa->prodi->fakultas ?? '',
            'institution' => "Universitas Islam Internasional Darullughah Wadda'wah",
            'city' => $signerData['city'] ?? 'Bangil',
            'kaprodi' => [
                'name' => $signerData['name'] ?? 'Nama Kaprodi',
                'nip' => $signerData['nip'] ?? '-',
                'position' => 'Kepala Program Studi',
                'signature' => $signerData['signature'] ?? null,
            ],
            'dekan' => [
                'name' => $signerData['dekan_name'] ?? 'Nama Dekan',
                'nip' => $signerData['dekan_nip'] ?? '-',
                'position' => 'Dekan Fakultas',
                'signature' => $signerData['dekan_signature'] ?? null,
            ],
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
            'skripsi.mahasiswa.prodi',
            'skripsi.pembimbing.dosen',
            'penguji.dosen',
            'beritaAcara'
        ]);

        $beritaAcara = $seminar->beritaAcara;
        if (!$beritaAcara) {
            return response()->json([
                'success' => false,
                'message' => "Berita Acara {$label} belum dibuat"
            ], 404);
        }

        $data = [
            'seminar' => $seminar,
            'beritaAcara' => $beritaAcara,
            'jenisLabel' => $label,
            'tanggal' => now()->translatedFormat('d F Y'),
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
}
