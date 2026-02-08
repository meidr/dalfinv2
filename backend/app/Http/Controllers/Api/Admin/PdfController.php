<?php

namespace App\Http\Controllers\Api\Admin;

use App\Http\Controllers\Controller;
use App\Models\Skripsi;
use App\Models\SKTugas;
use App\Models\NotaBimbingan;
use App\Models\BeritaAcara;
use App\Models\Seminar;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Request;

class PdfController extends Controller
{
    /**
     * Generate SK Tugas Pembimbing PDF
     */
    public function skTugas(Request $request, Skripsi $skripsi)
    {
        $skripsi->load(['mahasiswa.prodi', 'pembimbing.dosen']);

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

    /**
     * Generate Nota Bimbingan PDF
     */
    public function notaBimbingan(Request $request, Skripsi $skripsi)
    {
        $skripsi->load([
            'mahasiswa.prodi',
            'pembimbing.dosen',
            'bimbingan' => function ($q) {
                $q->where('status', 'approved')->orderBy('tanggal', 'asc');
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
            'skripsi.mahasiswa.prodi',
            'skripsi.pembimbing.dosen',
            'penguji.dosen',
            'beritaAcara'
        ]);

        $beritaAcara = $seminar->beritaAcara;
        if (!$beritaAcara) {
            return response()->json([
                'success' => false,
                'message' => 'Berita acara belum dibuat untuk seminar ini'
            ], 404);
        }

        $jenisLabel = $seminar->jenis === 'sempro' ? 'Seminar Proposal' : 'Seminar Hasil';

        $data = [
            'seminar' => $seminar,
            'beritaAcara' => $beritaAcara,
            'jenisLabel' => $jenisLabel,
            'tanggal' => now()->translatedFormat('d F Y'),
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
