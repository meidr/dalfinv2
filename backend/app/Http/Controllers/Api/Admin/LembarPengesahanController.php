<?php

namespace App\Http\Controllers\Api\Admin;

use App\Http\Controllers\Controller;
use App\Models\Seminar;
use App\Models\LembarPengesahan;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class LembarPengesahanController extends Controller
{
    /**
     * Display a listing of seminar sidang that have berita acara ready for lembar pengesahan.
     */
    public function index(Request $request)
    {
        $query = Seminar::with([
            'skripsi.mahasiswa',
            'beritaAcara',
            'lembarPengesahan'
        ])->where('jenis', 'sidang')
          ->where('status', 'selesai')
          ->whereHas('beritaAcara'); // Only show if Berita Acara has been generated

        // Search by mahasiswa name or nim
        if ($request->has('search') && $request->search != '') {
            $search = $request->search;
            $query->whereHas('skripsi.mahasiswa', function ($q) use ($search) {
                $q->where('nama', 'like', "%{$search}%")
                  ->orWhere('nim', 'like', "%{$search}%");
            });
        }

        // Filter by status: belum_cetak, selesai
        if ($request->has('status') && $request->status != '') {
            if ($request->status === 'belum_cetak') {
                $query->whereDoesntHave('lembarPengesahan');
            } elseif ($request->status === 'selesai') {
                $query->whereHas('lembarPengesahan');
            }
        }

        $query->orderBy('tanggal', 'desc');

        $perPage = $request->input('per_page', 10);
        $seminars = $query->paginate($perPage);

        // Calculate stats
        $statsQuery = Seminar::where('jenis', 'sidang')
            ->where('status', 'selesai')
            ->whereHas('beritaAcara');
            
        $total = (clone $statsQuery)->count();
        $sudahCetak = (clone $statsQuery)->whereHas('lembarPengesahan')->count();
        $siapGenerate = $total - $sudahCetak;

        return response()->json([
            'success' => true,
            'data' => $seminars,
            'stats' => [
                'total' => $total,
                'siap_generate' => $siapGenerate,
                'sudah_cetak' => $sudahCetak,
            ]
        ]);
    }

    /**
     * Generate Lembar Pengesahan and return PDF
     */
    public function generate(Request $request, Seminar $seminar)
    {
        if ($seminar->jenis !== 'sidang') {
            return response()->json([
                'success' => false,
                'message' => 'Lembar pengesahan hanya untuk ujian sidang'
            ], 400);
        }

        if (!$seminar->beritaAcara) {
            return response()->json([
                'success' => false,
                'message' => 'Berita Acara harus digenerate terlebih dahulu'
            ], 400);
        }

        try {
            DB::beginTransaction();

            // Create record if not exists
            if (!$seminar->lembarPengesahan) {
                $seminar->lembarPengesahan()->create([
                    'tanggal' => now()->toDateString()
                ]);
            }

            DB::commit();

            // Return PDF via PdfController
            $pdfController = app(\App\Http\Controllers\Api\Admin\PdfController::class);
            return $pdfController->lembarPengesahan($request, $seminar);
            
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'success' => false,
                'message' => 'Gagal generate Lembar Pengesahan',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Download PDF
     */
    public function downloadPdf(Request $request, Seminar $seminar)
    {
        if (!$seminar->lembarPengesahan) {
            return response()->json([
                'success' => false,
                'message' => 'Lembar Pengesahan belum digenerate'
            ], 404);
        }

        // Return PDF via PdfController
        $pdfController = app(\App\Http\Controllers\Api\Admin\PdfController::class);
        return $pdfController->lembarPengesahan($request, $seminar);
    }
}
