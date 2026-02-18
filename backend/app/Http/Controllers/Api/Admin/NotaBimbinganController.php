<?php

namespace App\Http\Controllers\Api\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Skripsi;
use App\Models\NotaBimbingan;
use Carbon\Carbon;

class NotaBimbinganController extends Controller
{
    public function index(Request $request)
    {
        // Query Skripsi that have pembimbing assigned (all active skripsi with bimbingan)
        $query = Skripsi::with([
            'mahasiswa.prodi',
            'pembimbing.dosen',
            'bimbingan' => function ($q) {
                $q->orderBy('tanggal', 'desc');
            },
            'notaBimbingan'
        ])->has('pembimbing');

        if ($request->filled('search')) {
            $search = $request->search;
            $query->whereHas('mahasiswa', function ($q) use ($search) {
                $q->where('nama', 'like', "%{$search}%")
                    ->orWhere('nim', 'like', "%{$search}%");
            });
        }

        $skripsiList = $query->orderBy('updated_at', 'desc')->paginate(10);

        // Transform data to include status and bimbingan info
        $skripsiList->getCollection()->transform(function ($item) {
            $bimbinganList = $item->bimbingan ?? collect();
            $approvedCount = $bimbinganList->where('status', 'approved')->count();
            $totalCount = $bimbinganList->count();

            // Determine nota status
            if ($item->notaBimbingan) {
                $item->nota_status = 'diterbitkan';
            } elseif ($approvedCount >= 8) {
                $item->nota_status = 'siap_cetak';
            } else {
                $item->nota_status = 'proses';
            }

            $item->total_bimbingan = $totalCount;
            $item->approved_bimbingan = $approvedCount;
            $item->tanggal_selesai = $bimbinganList->first()?->tanggal;

            return $item;
        });

        // Calculate stats
        $allSkripsiWithPembimbing = Skripsi::has('pembimbing')->get()->load('bimbingan', 'notaBimbingan');

        $siapCetak = 0;
        $menungguUpload = 0;
        $totalDiterbitkan = 0;

        foreach ($allSkripsiWithPembimbing as $s) {
            $approved = $s->bimbingan->where('status', 'approved')->count();
            if ($s->notaBimbingan) {
                $totalDiterbitkan++;
            } elseif ($approved >= 8) {
                $siapCetak++;
            } else {
                $menungguUpload++;
            }
        }

        $stats = [
            'siap_cetak' => $siapCetak,
            'menunggu_upload' => $menungguUpload,
            'total_diterbitkan' => $totalDiterbitkan,
        ];

        return response()->json([
            'success' => true,
            'data' => $skripsiList,
            'stats' => $stats
        ]);
    }

    /**
     * Export nota bimbingan data as CSV
     */
    public function export(Request $request)
    {
        $query = Skripsi::with([
            'mahasiswa.prodi',
            'pembimbing.dosen',
            'bimbingan',
            'notaBimbingan'
        ])->has('pembimbing');

        if ($request->filled('search')) {
            $search = $request->search;
            $query->whereHas('mahasiswa', function ($q) use ($search) {
                $q->where('nama', 'like', "%{$search}%")
                    ->orWhere('nim', 'like', "%{$search}%");
            });
        }

        $skripsiList = $query->orderBy('updated_at', 'desc')->get();

        // Build CSV
        $headers = [
            'Content-Type' => 'text/csv; charset=UTF-8',
            'Content-Disposition' => 'attachment; filename="nota_bimbingan_' . date('Y-m-d') . '.csv"',
        ];

        $callback = function () use ($skripsiList) {
            $file = fopen('php://output', 'w');

            // UTF-8 BOM for Excel
            fprintf($file, chr(0xEF) . chr(0xBB) . chr(0xBF));

            // Header row
            fputcsv($file, [
                'No',
                'NIM',
                'Nama Mahasiswa',
                'Program Studi',
                'Judul Skripsi',
                'Pembimbing 1',
                'Pembimbing 2',
                'Total Bimbingan',
                'Bimbingan Disetujui',
                'Status Nota',
                'Tanggal Terakhir Bimbingan',
            ]);

            foreach ($skripsiList as $index => $item) {
                $bimbinganList = $item->bimbingan ?? collect();
                $approvedCount = $bimbinganList->where('status', 'approved')->count();
                $totalCount = $bimbinganList->count();

                $pembimbing1 = $item->pembimbing->where('jenis', 'pembimbing_1')->first();
                $pembimbing2 = $item->pembimbing->where('jenis', 'pembimbing_2')->first();

                if ($item->notaBimbingan) {
                    $notaStatus = 'Diterbitkan';
                } elseif ($approvedCount >= 8) {
                    $notaStatus = 'Siap Cetak';
                } else {
                    $notaStatus = 'Proses';
                }

                $lastBimbingan = $bimbinganList->sortByDesc('tanggal')->first();

                fputcsv($file, [
                    $index + 1,
                    $item->mahasiswa->nim ?? '-',
                    $item->mahasiswa->nama ?? '-',
                    $item->mahasiswa->prodi->nama ?? '-',
                    $item->judul ?? '-',
                    $pembimbing1?->dosen->full_name ?? '-',
                    $pembimbing2?->dosen->full_name ?? '-',
                    $totalCount,
                    $approvedCount,
                    $notaStatus,
                    $lastBimbingan ? Carbon::parse($lastBimbingan->tanggal)->format('d/m/Y') : '-',
                ]);
            }

            fclose($file);
        };

        return response()->stream($callback, 200, $headers);
    }
}
