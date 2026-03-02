<?php

namespace App\Http\Controllers\Api\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Seminar;
use App\Models\BeritaAcara;
use Barryvdh\DomPDF\Facade\Pdf;
use Carbon\Carbon;

class BeritaAcaraController extends Controller
{
    public function index(Request $request)
    {
        $query = Seminar::with([
            'skripsi.mahasiswa.prodi',
            'skripsi.pembimbing.dosen',
            'penguji.dosen',
            'beritaAcara'
        ])->where('jenis', 'sidang')
            ->whereIn('status', ['selesai', 'pending']);

        if ($request->filled('search')) {
            $search = $request->search;
            $query->whereHas('skripsi.mahasiswa', function ($q) use ($search) {
                $q->where('nama', 'like', "%{$search}%")
                    ->orWhere('nim', 'like', "%{$search}%");
            });
        }

        if ($request->filled('status')) {
            if ($request->status === 'belum_cetak') {
                $query->whereDoesntHave('beritaAcara');
            } elseif ($request->status === 'selesai') {
                $query->whereHas('beritaAcara');
            }
        }

        $seminars = $query->orderBy('tanggal', 'desc')->paginate(10);

        $seminars->getCollection()->transform(function ($item) {
            $item->tanggal_ujian = $item->tanggal;
            $item->waktu_ujian = $item->waktu;
            $item->berita_acara_tercetak = $item->beritaAcara !== null;
            return $item;
        });

        $stats = [
            'siap_generate' => Seminar::where('jenis', 'sidang')
                ->where('status', 'selesai')
                ->whereIn('hasil', ['lulus', 'lulus_revisi'])
                ->whereDoesntHave('beritaAcara')
                ->count(),
            'sudah_cetak' => BeritaAcara::count(),
            'total' => Seminar::where('jenis', 'sidang')->count(),
        ];

        return response()->json([
            'success' => true,
            'data' => $seminars,
            'stats' => $stats
        ]);
    }

    /**
     * Generate Berita Acara for a seminar/ujian
     * Creates a BeritaAcara record and returns the PDF
     */
    public function generate(Request $request, Seminar $seminar)
    {
        // Check if already generated — just re-download PDF
        $seminar->load('beritaAcara');
        if ($seminar->beritaAcara) {
            return $this->downloadPdf($request, $seminar);
        }

        // Generate a unique nomor BA
        $year = now()->year;
        $romanMonth = $this->getRomanMonth();
        $count = BeritaAcara::whereYear('created_at', $year)->count() + 1;
        $nomor = sprintf('BA/%03d/%s/%d', $count, $romanMonth, $year);

        // Ensure nomor is truly unique (increment if collision)
        while (BeritaAcara::where('nomor', $nomor)->exists()) {
            $count++;
            $nomor = sprintf('BA/%03d/%s/%d', $count, $romanMonth, $year);
        }

        // Create berita acara record
        $beritaAcara = BeritaAcara::create([
            'jenis' => 'seminar',
            'seminar_id' => $seminar->id,
            'nomor' => $nomor,
            'tanggal' => now()->toDateString(),
            'hasil' => $seminar->hasil ?? 'lulus',
            'catatan' => $seminar->catatan,
        ]);

        return $this->downloadPdf($request, $seminar);
    }

    /**
     * Download Berita Acara PDF
     */
    public function downloadPdf(Request $request, Seminar $seminar)
    {
        // Delegate to PdfController which has full QR + signature support
        $pdfController = app(\App\Http\Controllers\Api\Admin\PdfController::class);
        return $pdfController->beritaAcaraSeminar($request, $seminar);
    }

    /**
     * Export berita acara data as CSV/Excel
     */
    public function exportExcel(Request $request)
    {
        $query = Seminar::with([
            'skripsi.mahasiswa.prodi',
            'skripsi.pembimbing.dosen',
            'penguji.dosen',
            'beritaAcara'
        ])->where('jenis', 'sidang')
            ->whereIn('status', ['selesai', 'pending'])
            ->orderBy('tanggal', 'desc');

        if ($request->filled('status')) {
            if ($request->status === 'belum_cetak') {
                $query->whereDoesntHave('beritaAcara');
            } elseif ($request->status === 'selesai') {
                $query->whereHas('beritaAcara');
            }
        }

        $data = $query->get();

        // Generate CSV
        $headers = [
            'Content-Type' => 'text/csv; charset=UTF-8',
            'Content-Disposition' => 'attachment; filename="Rekap_Berita_Acara.csv"',
        ];

        $callback = function () use ($data) {
            $file = fopen('php://output', 'w');
            // BOM for Excel UTF-8 compatibility
            fprintf($file, chr(0xEF) . chr(0xBB) . chr(0xBF));

            // Header row
            fputcsv($file, [
                'No',
                'Nomor BA',
                'NIM',
                'Nama Mahasiswa',
                'Program Studi',
                'Judul Skripsi',
                'Tanggal Ujian',
                'Waktu',
                'Ruangan',
                'Pembimbing 1',
                'Pembimbing 2',
                'Penguji',
                'Nilai',
                'Hasil',
                'Status BA',
            ]);

            $no = 1;
            foreach ($data as $item) {
                $pembimbing = $item->skripsi->pembimbing ?? collect();
                $pb1 = $pembimbing->where('jenis', 'pembimbing_1')->first();
                $pb2 = $pembimbing->where('jenis', 'pembimbing_2')->first();

                $pengujiNames = collect($item->penguji ?? [])->map(function ($p) {
                    return ($p->dosen->full_name ?? $p->dosen->nama ?? '-') . ' (' . ucfirst($p->peran) . ')';
                })->implode('; ');

                fputcsv($file, [
                    $no++,
                    $item->beritaAcara?->nomor ?? '-',
                    $item->skripsi->mahasiswa->nim ?? '-',
                    $item->skripsi->mahasiswa->nama ?? '-',
                    $item->skripsi->mahasiswa->prodi->nama ?? '-',
                    $item->skripsi->judul ?? '-',
                    $item->tanggal ? Carbon::parse($item->tanggal)->format('d/m/Y') : '-',
                    $item->waktu ? Carbon::parse($item->waktu)->format('H:i') : '-',
                    $item->ruangan ?? '-',
                    $pb1?->dosen?->full_name ?? ($pb1?->dosen?->nama ?? '-'),
                    $pb2?->dosen?->full_name ?? ($pb2?->dosen?->nama ?? '-'),
                    $pengujiNames,
                    $item->nilai ?? '-',
                    ucfirst(str_replace('_', ' ', $item->hasil ?? '-')),
                    $item->beritaAcara ? 'Sudah Dicetak' : 'Belum Dicetak',
                ]);
            }

            fclose($file);
        };

        return response()->stream($callback, 200, $headers);
    }

    /**
     * Get Roman numeral month
     */
    private function getRomanMonth(): string
    {
        $months = [
            1 => 'I',
            2 => 'II',
            3 => 'III',
            4 => 'IV',
            5 => 'V',
            6 => 'VI',
            7 => 'VII',
            8 => 'VIII',
            9 => 'IX',
            10 => 'X',
            11 => 'XI',
            12 => 'XII'
        ];
        return $months[now()->month];
    }
}
