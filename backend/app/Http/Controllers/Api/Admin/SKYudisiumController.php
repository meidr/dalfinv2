<?php

namespace App\Http\Controllers\Api\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Seminar;
use App\Models\Skripsi;
use App\Models\SKYudisium;
use App\Models\Prodi;
use App\Services\NomorSuratService;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;

class SKYudisiumController extends Controller
{
    public function __construct(private ?NomorSuratService $nomorSuratService = null)
    {
        $this->nomorSuratService ??= app(NomorSuratService::class);
    }

    public function index(Request $request)
    {
        // Get seminars (ujian) that are completed with 'lulus' status
        $query = Seminar::with([
            'skripsi.mahasiswa.prodi',
            'skripsi.pembimbing.dosen',
            'skripsi.skYudisium',
            'penguji.dosen',
            'beritaAcara'
        ])->where('jenis', 'sidang')
            ->where('status', 'selesai')
            ->whereIn('hasil', ['lulus', 'lulus_revisi']);

        if ($request->filled('search')) {
            $search = $request->search;
            $query->whereHas('skripsi.mahasiswa', function ($q) use ($search) {
                $q->where('nama', 'like', "%{$search}%")
                    ->orWhere('nim', 'like', "%{$search}%");
            });
        }

        // Filter by tahun akademik (e.g. "2024/2025")
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

        $perPage = max(5, min((int) $request->get('per_page', 10), 100));
        $seminars = $query->orderBy('tanggal', 'desc')->paginate($perPage);

        // Transform data - set status based on SKYudisium existence
        $seminars->getCollection()->transform(function ($item) {
            $item->tanggal_ujian = $item->tanggal;
            $item->sk_yudisium = $item->skripsi?->skYudisium;
            $item->status = $item->skripsi?->skYudisium ? 'sk_terbit' : 'siap_yudisium';
            return $item;
        });

        // Calculate stats
        $currentYear = Carbon::now()->year;
        $currentMonth = Carbon::now()->month;

        $lulusQuery = Seminar::where('jenis', 'sidang')
            ->where('status', 'selesai')
            ->whereIn('hasil', ['lulus', 'lulus_revisi']);

        $siapYudisium = (clone $lulusQuery)->whereHas('skripsi', function ($q) {
            $q->whereDoesntHave('skYudisium');
        })->count();

        $skTerbitBulanIni = SKYudisium::whereMonth('tanggal_terbit', $currentMonth)
            ->whereYear('tanggal_terbit', $currentYear)
            ->count();

        $totalLulusan = SKYudisium::whereYear('tanggal_terbit', $currentYear)->count();

        // Calculate percentage increase vs last year
        $lastYearTotal = SKYudisium::whereYear('tanggal_terbit', $currentYear - 1)->count();
        $persentase = $lastYearTotal > 0
            ? round((($totalLulusan - $lastYearTotal) / $lastYearTotal) * 100)
            : ($totalLulusan > 0 ? 100 : 0);

        $stats = [
            'siap_yudisium' => $siapYudisium,
            'sk_terbit_bulan_ini' => $skTerbitBulanIni,
            'total_lulusan' => $totalLulusan,
            'persentase_kenaikan' => $persentase,
        ];

        return response()->json([
            'success' => true,
            'data' => $seminars,
            'stats' => $stats
        ]);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'skripsi_id' => 'required|exists:skripsi,id',
            'nomor_sk' => 'nullable|string',
            'tanggal' => 'required|date',
            'ipk' => 'required|numeric|min:0|max:4',
            'predikat' => 'required|string|in:memuaskan,sangat_memuaskan,cum_laude',
        ]);

        $skripsi = Skripsi::findOrFail($validated['skripsi_id']);

        // Check if already exists
        if ($skripsi->skYudisium) {
            return response()->json([
                'success' => false,
                'message' => 'SK Yudisium sudah pernah diterbitkan untuk skripsi ini'
            ], 422);
        }

        $skYudisium = DB::transaction(function () use ($skripsi, $validated) {
            $nomorSk = $this->nomorSuratService->generateForSkripsi('sk_yudisium', $skripsi, Carbon::parse($validated['tanggal']));

            // Create SK Yudisium record
            $skYudisium = SKYudisium::create([
                'skripsi_id' => $skripsi->id,
                'nomor_sk' => $nomorSk,
                'tanggal_terbit' => $validated['tanggal'],
                'tanggal_yudisium' => $validated['tanggal'],
                'predikat' => $validated['predikat'],
                'ipk_akhir' => $validated['ipk'],
            ]);

            // Update skripsi status
            $skripsi->update(['status' => 'lulus']);

            // Update mahasiswa status
            $skripsi->mahasiswa()->update([
                'status' => 'lulus',
            ]);

            return $skYudisium;
        });

        return response()->json([
            'success' => true,
            'message' => 'SK Yudisium berhasil diproses',
            'data' => $skYudisium
        ], 201);
    }

    public function exportExcel(Request $request)
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

        $data = $query->get();

        $headers = [
            'Content-Type' => 'text/csv; charset=UTF-8',
            'Content-Disposition' => 'attachment; filename="Rekap_SK_Yudisium.csv"',
        ];

        $callback = function () use ($data) {
            $file = fopen('php://output', 'w');
            fprintf($file, chr(0xEF) . chr(0xBB) . chr(0xBF));

            fputcsv($file, [
                'No',
                'NIM',
                'Nama Mahasiswa',
                'Program Studi',
                'Judul Skripsi',
                'Tanggal Ujian',
                'Nilai',
                'Nomor SK',
                'Tanggal SK',
                'IPK',
                'Predikat',
                'Status',
            ]);

            $no = 1;
            foreach ($data as $item) {
                $sk = $item->skripsi?->skYudisium;
                $predikat = $sk?->predikat;
                if ($predikat) {
                    $predikat = ucwords(str_replace('_', ' ', $predikat));
                }

                fputcsv($file, [
                    $no++,
                    $item->skripsi->mahasiswa->nim ?? '-',
                    $item->skripsi->mahasiswa->nama ?? '-',
                    $item->skripsi->mahasiswa->prodi->nama ?? '-',
                    $item->skripsi->judul ?? '-',
                    $item->tanggal ? Carbon::parse($item->tanggal)->format('d/m/Y') : '-',
                    $item->nilai ?? '-',
                    $sk?->nomor_sk ?? '-',
                    $sk?->tanggal_terbit ? Carbon::parse($sk->tanggal_terbit)->format('d/m/Y') : '-',
                    $sk?->ipk_akhir ?? '-',
                    $predikat ?? '-',
                    $sk ? 'SK Terbit' : 'Siap Yudisium',
                ]);
            }

            fclose($file);
        };

        return response()->stream($callback, 200, $headers);
    }

    /**
     * Get list of SK Yudisium batches (distinct nomor_sk_batch)
     */
    public function batchIndex(Request $request)
    {
        $query = SKYudisium::whereNotNull('nomor_sk_batch')
            ->select('nomor_sk_batch', 'th_akademik_id', 'tanggal_terbit', 'tanggal_yudisium')
            ->selectRaw('COUNT(*) as jumlah_mahasiswa')
            ->selectRaw('MIN(id) as id')
            ->groupBy('nomor_sk_batch', 'th_akademik_id', 'tanggal_terbit', 'tanggal_yudisium');

        if ($request->filled('search')) {
            $query->where('nomor_sk_batch', 'like', "%{$request->search}%");
        }

        $perPage = max(5, min((int) $request->get('per_page', 10), 100));
        $batches = $query->orderBy('tanggal_terbit', 'desc')->paginate($perPage);

        // Load tahun akademik names
        $tahunIds = $batches->pluck('th_akademik_id')->filter()->unique();
        $tahuns = \App\Models\Tahun::whereIn('id', $tahunIds)->pluck('name', 'id');

        $batches->getCollection()->transform(function ($item) use ($tahuns) {
            $item->tahun_akademik_name = $tahuns[$item->th_akademik_id] ?? '-';
            return $item;
        });

        return response()->json([
            'success' => true,
            'data' => $batches,
        ]);
    }

    /**
     * Create a new SK Yudisium batch
     */
    public function storeBatch(Request $request)
    {
        $validated = $request->validate([
            'nomor_sk_batch' => 'nullable|string|max:255',
            'th_akademik_id' => 'required|exists:tahuns,id',
            'prodi_id' => 'nullable|exists:prodi,id',
            'tanggal_terbit' => 'required|date',
            'tanggal_yudisium' => 'required|date',
        ]);

        $prodi = isset($validated['prodi_id']) ? Prodi::with('fakultas')->find($validated['prodi_id']) : null;
        $validated['nomor_sk_batch'] = $this->nomorSuratService->generateForProdi(
            'sk_yudisium',
            $prodi,
            Carbon::parse($validated['tanggal_terbit'])
        );

        // Check if batch already exists
        $exists = SKYudisium::where('nomor_sk_batch', $validated['nomor_sk_batch'])->exists();
        if ($exists) {
            return response()->json([
                'success' => false,
                'message' => 'Nomor SK Batch sudah digunakan',
            ], 422);
        }

        return response()->json([
            'success' => true,
            'message' => 'Batch SK Yudisium berhasil dibuat',
            'data' => $validated,
        ], 201);
    }

    /**
     * Get detail of a specific batch
     */
    public function batchDetail(Request $request, string $nomor)
    {
        $nomor = urldecode($nomor);

        // Get mahasiswa assigned to this batch
        $assigned = SKYudisium::with(['skripsi.mahasiswa.prodi', 'tahunAkademik', 'prodi.fakultas'])
            ->where('nomor_sk_batch', $nomor)
            ->get();

        // Get batch info from first record
        $batchInfo = $assigned->first();

        // Get mahasiswa siap yudisium (lulus sidang, either no SK Yudisium yet OR SK without batch)
        $unassignedQuery = Seminar::with([
            'skripsi.mahasiswa.prodi',
            'skripsi.skYudisium',
        ])->where('jenis', 'sidang')
            ->where('status', 'selesai')
            ->whereIn('hasil', ['lulus', 'lulus_revisi'])
            ->where(function ($q) {
                $q->whereDoesntHave('skripsi.skYudisium')
                    ->orWhereHas('skripsi.skYudisium', function ($sq) {
                        $sq->whereNull('nomor_sk_batch')
                            ->orWhere('nomor_sk_batch', '');
                    });
            });

        if ($request->filled('search')) {
            $search = $request->search;
            $unassignedQuery->whereHas('skripsi.mahasiswa', function ($q) use ($search) {
                $q->where('nama', 'like', "%{$search}%")
                    ->orWhere('nim', 'like', "%{$search}%");
            });
        }

        // Filter by fakultas
        if ($request->filled('fakultas_id')) {
            $unassignedQuery->whereHas('skripsi.mahasiswa.prodi', function ($q) use ($request) {
                $q->where('fakultas_id', $request->fakultas_id);
            });
        }

        // Filter by prodi
        if ($request->filled('prodi_id')) {
            $unassignedQuery->whereHas('skripsi.mahasiswa', function ($q) use ($request) {
                $q->where('prodi_id', $request->prodi_id);
            });
        }

        // Filter by jenis kelamin
        if ($request->filled('jenis_kelamin')) {
            $unassignedQuery->whereHas('skripsi.mahasiswa', function ($q) use ($request) {
                $q->where('jenis_kelamin', $request->jenis_kelamin);
            });
        }

        $unassigned = $unassignedQuery->orderBy('tanggal', 'desc')->get();

        return response()->json([
            'success' => true,
            'batch_info' => $batchInfo ? [
                'nomor_sk_batch' => $batchInfo->nomor_sk_batch,
                'th_akademik_id' => $batchInfo->th_akademik_id,
                'tahun_akademik_name' => $batchInfo->tahunAkademik?->name ?? '-',
                'prodi_id' => $batchInfo->prodi_id,
                'prodi_name' => $batchInfo->prodi?->nama ?? '-',
                'fakultas_name' => $batchInfo->prodi?->fakultas?->nama_fakultas ?? '-',
                'tanggal_terbit' => $batchInfo->tanggal_terbit,
                'tanggal_yudisium' => $batchInfo->tanggal_yudisium,
            ] : null,
            'assigned' => $assigned,
            'unassigned' => $unassigned,
        ]);
    }

    /**
     * Update batch info (all sk_yudisium records with given nomor_sk_batch)
     */
    public function updateBatch(Request $request, string $nomor)
    {
        $nomor = urldecode($nomor);

        $validated = $request->validate([
            'th_akademik_id' => 'required|exists:tahuns,id',
            'prodi_id' => 'nullable|exists:prodi,id',
            'tanggal_terbit' => 'required|date',
            'tanggal_yudisium' => 'required|date',
        ]);

        $records = SKYudisium::where('nomor_sk_batch', $nomor)->get();

        if ($records->isEmpty()) {
            return response()->json([
                'success' => false,
                'message' => 'Batch tidak ditemukan',
            ], 404);
        }

        DB::transaction(function () use ($records, $validated) {
            foreach ($records as $sk) {
                $sk->update([
                    'th_akademik_id' => $validated['th_akademik_id'],
                    'prodi_id' => $validated['prodi_id'] ?? null,
                    'tanggal_terbit' => $validated['tanggal_terbit'],
                    'tanggal_yudisium' => $validated['tanggal_yudisium'],
                ]);
            }
        });

        return response()->json([
            'success' => true,
            'message' => 'Batch berhasil diperbarui',
        ]);
    }

    /**
     * Assign mahasiswa to a batch (creates sk_yudisium records)
     */
    public function assignBatch(Request $request)
    {
        $validated = $request->validate([
            'nomor_sk_batch' => 'nullable|string',
            'th_akademik_id' => 'required|exists:tahuns,id',
            'prodi_id' => 'nullable|exists:prodi,id',
            'tanggal_terbit' => 'required|date',
            'tanggal_yudisium' => 'required|date',
            'skripsi_ids' => 'required|array|min:1',
            'skripsi_ids.*' => 'required|exists:skripsi,id',
        ]);

        $created = DB::transaction(function () use ($validated) {
            $records = [];
            $firstSkripsi = Skripsi::with('mahasiswa.prodi.fakultas')->findOrFail($validated['skripsi_ids'][0]);
            $validated['nomor_sk_batch'] = $validated['nomor_sk_batch'] ?: $this->nomorSuratService->generateForSkripsi(
                'sk_yudisium',
                $firstSkripsi,
                Carbon::parse($validated['tanggal_terbit'])
            );

            foreach ($validated['skripsi_ids'] as $skripsiId) {
                $skripsi = Skripsi::findOrFail($skripsiId);
                $existingSk = $skripsi->skYudisium;

                // If already has sk_yudisium with a batch assigned, skip
                if ($existingSk && $existingSk->nomor_sk_batch) {
                    continue;
                }

                if ($existingSk) {
                    $this->nomorSuratService->ensureSkYudisiumNumber(
                        $existingSk,
                        $skripsi,
                        Carbon::parse($validated['tanggal_terbit'])
                    );

                    // Update existing record that has no batch
                    $existingSk->update([
                        'nomor_sk_batch' => $validated['nomor_sk_batch'],
                        'th_akademik_id' => $validated['th_akademik_id'],
                        'prodi_id' => $validated['prodi_id'] ?? null,
                        'tanggal_terbit' => $validated['tanggal_terbit'],
                        'tanggal_yudisium' => $validated['tanggal_yudisium'],
                    ]);
                    $sk = $existingSk;
                } else {
                    $nomorSk = $this->nomorSuratService->generateForSkripsi(
                        'sk_yudisium',
                        $skripsi,
                        Carbon::parse($validated['tanggal_terbit'])
                    );

                    // Create new record
                    $sk = SKYudisium::create([
                        'skripsi_id' => $skripsi->id,
                        'nomor_sk' => $nomorSk,
                        'nomor_sk_batch' => $validated['nomor_sk_batch'],
                        'th_akademik_id' => $validated['th_akademik_id'],
                        'prodi_id' => $validated['prodi_id'] ?? null,
                        'tanggal_terbit' => $validated['tanggal_terbit'],
                        'tanggal_yudisium' => $validated['tanggal_yudisium'],
                    ]);
                }

                // Update skripsi & mahasiswa status
                $skripsi->update(['status' => 'lulus']);
                $skripsi->mahasiswa()->update(['status' => 'lulus']);

                $records[] = $sk;
            }

            return $records;
        });

        return response()->json([
            'success' => true,
            'message' => count($created) . ' mahasiswa berhasil diassign ke batch',
            'data' => $created,
        ]);
    }

    /**
     * Remove a mahasiswa from a batch (reset nomor_sk_batch only)
     */
    public function removeBatch(int $id)
    {
        $skYudisium = SKYudisium::findOrFail($id);

        $skYudisium->update([
            'nomor_sk_batch' => null,
            'prodi_id' => null,
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Mahasiswa berhasil dikeluarkan dari batch',
        ]);
    }

    /**
     * Delete an entire batch (reset nomor_sk_batch for all records in batch)
     */
    public function destroyBatch(string $nomor)
    {
        $nomor = urldecode($nomor);

        $count = SKYudisium::where('nomor_sk_batch', $nomor)->count();

        if ($count === 0) {
            return response()->json([
                'success' => false,
                'message' => 'Batch tidak ditemukan',
            ], 404);
        }

        SKYudisium::where('nomor_sk_batch', $nomor)->update([
            'nomor_sk_batch' => null,
            'prodi_id' => null,
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Batch SK Yudisium berhasil dihapus (' . $count . ' mahasiswa dikembalikan)',
        ]);
    }
}
