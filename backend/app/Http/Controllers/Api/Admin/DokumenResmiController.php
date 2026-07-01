<?php

namespace App\Http\Controllers\Api\Admin;

use App\Http\Controllers\Controller;
use App\Models\BeritaAcara;
use App\Models\Fakultas;
use App\Models\NotaBimbingan;
use App\Models\Prodi;
use App\Models\Seminar;
use App\Models\SKTugas;
use App\Models\SKYudisium;
use App\Traits\GenderFilterable;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Str;
use RuntimeException;
use ZipArchive;

class DokumenResmiController extends Controller
{
    use GenderFilterable;

    private const TYPES = [
        'sk_penguji_sempro' => [
            'label' => 'SK Penguji Seminar Proposal',
            'category' => 'sk_penguji',
            'jenis' => 'sempro',
        ],
        'ba_sempro' => [
            'label' => 'Berita Acara Seminar Proposal',
            'category' => 'berita_acara',
            'jenis' => 'sempro',
        ],
        'sk_tugas' => [
            'label' => 'SK Tugas Pembimbing',
            'category' => 'sk_tugas',
        ],
        'nota_bimbingan' => [
            'label' => 'Nota Bimbingan Skripsi',
            'category' => 'nota_bimbingan',
        ],
        'sk_penguji_semhas' => [
            'label' => 'SK Penguji Seminar Hasil',
            'category' => 'sk_penguji',
            'jenis' => 'semhas',
        ],
        'ba_semhas' => [
            'label' => 'Berita Acara Seminar Hasil',
            'category' => 'berita_acara',
            'jenis' => 'semhas',
        ],
        'sk_penguji_sidang' => [
            'label' => 'SK Penguji Sidang Skripsi',
            'category' => 'sk_penguji',
            'jenis' => 'sidang',
        ],
        'ba_sidang' => [
            'label' => 'Berita Acara Sidang Skripsi',
            'category' => 'berita_acara',
            'jenis' => 'sidang',
        ],
        'sk_yudisium' => [
            'label' => 'SK Yudisium',
            'category' => 'sk_yudisium',
        ],
        'lembar_pengesahan' => [
            'label' => 'Lembar Pengesahan',
            'category' => 'lembar_pengesahan',
            'jenis' => 'sidang',
        ],
        'jadwal_ujian' => [
            'label' => 'Jadwal',
            'category' => 'jadwal_ujian',
        ],
    ];

    public function index(Request $request)
    {
        $type = $request->get('type', 'sk_penguji_sempro');
        abort_unless(isset(self::TYPES[$type]), 404, 'Jenis dokumen tidak dikenal');

        $meta = self::TYPES[$type];
        $perPage = (int) $request->get('per_page', 10);
        $perPage = max(5, min($perPage, 100));

        $paginator = match ($meta['category']) {
            'sk_tugas' => $this->skTugas($request, $perPage),
            'nota_bimbingan' => $this->notaBimbingan($request, $perPage),
            'sk_yudisium' => $this->skYudisium($request, $perPage),
            'jadwal_ujian' => $this->jadwalUjian($request, $perPage),
            'sk_penguji' => $this->skPenguji($request, $meta['jenis'], $perPage),
            'berita_acara' => $this->beritaAcara($request, $meta['jenis'], $perPage),
            'lembar_pengesahan' => $this->lembarPengesahan($request, $perPage),
        };

        $paginator->getCollection()->transform(fn ($item) => $this->formatItem($item, $type, $meta));

        return response()->json([
            'success' => true,
            'types' => $this->types($request),
            'data' => $paginator,
        ]);
    }

    public function batchDownload(Request $request)
    {
        $validated = $request->validate([
            'type' => ['required', 'string'],
            'ids' => ['required', 'array', 'min:1', 'max:100'],
            'ids.*' => ['integer'],
        ]);

        $type = $validated['type'];
        abort_unless(isset(self::TYPES[$type]), 404, 'Jenis dokumen tidak dikenal');
        abort_if($type === 'jadwal_ujian', 422, 'Jadwal ujian tidak tersedia untuk download batch');

        if (!class_exists(ZipArchive::class)) {
            return response()->json([
                'success' => false,
                'message' => 'Ekstensi ZIP belum aktif di server',
            ], 500);
        }

        $ids = collect($validated['ids'])
            ->map(fn ($id) => (int) $id)
            ->filter()
            ->unique()
            ->values()
            ->all();

        $meta = self::TYPES[$type];
        $query = $this->queryForType($meta)->whereIn('id', $ids);
        $this->applySkripsiFilters($query, $request, $this->relationForCategory($meta['category']));
        $this->applyDateFilter($query, $request, $this->dateColumnForCategory($meta['category']));

        $order = array_flip($ids);
        $items = $query->get()
            ->sortBy(fn ($item) => $order[$item->id] ?? PHP_INT_MAX)
            ->values();

        if ($items->isEmpty()) {
            return response()->json([
                'success' => false,
                'message' => 'Tidak ada dokumen terpilih yang cocok dengan filter saat ini',
            ], 422);
        }

        $zipDir = storage_path('app/temp/dokumen-resmi');
        File::ensureDirectoryExists($zipDir);

        $zipFileName = $this->batchZipFileName($request, $type);
        $zipPath = $zipDir . DIRECTORY_SEPARATOR . uniqid('batch_', true) . '.zip';
        $zip = new ZipArchive();

        if ($zip->open($zipPath, ZipArchive::CREATE | ZipArchive::OVERWRITE) !== true) {
            return response()->json([
                'success' => false,
                'message' => 'Gagal membuat file ZIP',
            ], 500);
        }

        $usedFileNames = [];
        $failures = [];

        foreach ($items as $item) {
            $formatted = $this->formatItem($item, $type, $meta);

            try {
                $response = $this->pdfResponseForItem($request, $item, $meta);
                $contentType = (string) $response->headers->get('Content-Type', '');
                if ($response->getStatusCode() >= 400 || str_contains($contentType, 'application/json')) {
                    throw new RuntimeException($response->getContent() ?: 'Gagal membuat PDF');
                }

                $pdfContent = $response->getContent();
                if (!$pdfContent) {
                    throw new RuntimeException('PDF kosong');
                }

                $fileName = $this->uniqueZipEntryName(
                    $this->documentFileName($formatted),
                    $usedFileNames
                );
                $zip->addFromString($fileName, $pdfContent);
            } catch (\Throwable $error) {
                $failures[] = sprintf(
                    '%s - %s (%s)',
                    $formatted['mahasiswa']['nim'] ?? '-',
                    $formatted['mahasiswa']['nama'] ?? '-',
                    $error->getMessage()
                );
            }
        }

        if (!empty($failures)) {
            $zip->addFromString(
                '_gagal_generate.txt',
                "Beberapa dokumen gagal dibuat:\n" . implode("\n", $failures)
            );
        }

        $zip->close();

        if (empty($usedFileNames)) {
            File::delete($zipPath);

            return response()->json([
                'success' => false,
                'message' => 'Semua dokumen terpilih gagal dibuat',
            ], 422);
        }

        return response()->download($zipPath, $zipFileName, [
            'Content-Type' => 'application/zip',
        ])->deleteFileAfterSend(true);
    }

    private function queryForType(array $meta)
    {
        return match ($meta['category']) {
            'sk_tugas' => SKTugas::with('skripsi.mahasiswa.prodi')
                ->whereHas('skripsi'),
            'nota_bimbingan' => NotaBimbingan::with('skripsi.mahasiswa.prodi')
                ->whereHas('skripsi'),
            'sk_yudisium' => SKYudisium::with('skripsi.mahasiswa.prodi')
                ->whereHas('skripsi'),
            'jadwal_ujian' => Seminar::with(['skripsi.mahasiswa.prodi', 'penguji'])
                ->where('jenis', 'sidang')
                ->whereHas('skripsi'),
            'sk_penguji' => Seminar::with(['skripsi.mahasiswa.prodi', 'penguji'])
                ->where('jenis', $meta['jenis'])
                ->whereHas('penguji')
                ->whereHas('skripsi'),
            'lembar_pengesahan' => Seminar::with(['skripsi.mahasiswa.prodi', 'lembarPengesahan'])
                ->where('jenis', 'sidang')
                ->whereHas('lembarPengesahan')
                ->whereHas('skripsi'),
            'berita_acara' => BeritaAcara::with('seminar.skripsi.mahasiswa.prodi')
                ->whereHas('seminar', fn ($q) => $q->where('jenis', $meta['jenis'])),
        };
    }

    private function skTugas(Request $request, int $perPage)
    {
        $query = SKTugas::with('skripsi.mahasiswa.prodi')
            ->whereHas('skripsi');

        $this->applySkripsiFilters($query, $request, 'skripsi');
        $this->applyDateFilter($query, $request, 'tanggal_terbit');

        return $query->latest('tanggal_terbit')->paginate($perPage);
    }

    private function notaBimbingan(Request $request, int $perPage)
    {
        $query = NotaBimbingan::with('skripsi.mahasiswa.prodi')
            ->whereHas('skripsi');

        $this->applySkripsiFilters($query, $request, 'skripsi');
        $this->applyDateFilter($query, $request, 'tanggal_terbit');

        return $query->latest('tanggal_terbit')->paginate($perPage);
    }

    private function skYudisium(Request $request, int $perPage)
    {
        $query = SKYudisium::with('skripsi.mahasiswa.prodi')
            ->whereHas('skripsi');

        $this->applySkripsiFilters($query, $request, 'skripsi');
        $this->applyDateFilter($query, $request, 'tanggal_terbit');

        return $query->latest('tanggal_terbit')->paginate($perPage);
    }

    private function jadwalUjian(Request $request, int $perPage)
    {
        $query = Seminar::with(['skripsi.mahasiswa.prodi', 'penguji'])
            ->where('jenis', 'sidang')
            ->whereHas('skripsi');

        $this->applySkripsiFilters($query, $request, 'skripsi');
        $this->applyDateFilter($query, $request, 'tanggal');

        return $query->latest('tanggal')->paginate($perPage);
    }

    private function skPenguji(Request $request, string $jenis, int $perPage)
    {
        $query = Seminar::with(['skripsi.mahasiswa.prodi', 'penguji'])
            ->where('jenis', $jenis)
            ->whereHas('penguji')
            ->whereHas('skripsi');

        $this->applySkripsiFilters($query, $request, 'skripsi');
        $this->applyDateFilter($query, $request, 'tanggal');

        return $query->latest('tanggal')->paginate($perPage);
    }

    private function beritaAcara(Request $request, string $jenis, int $perPage)
    {
        $query = BeritaAcara::with('seminar.skripsi.mahasiswa.prodi')
            ->whereHas('seminar', fn ($q) => $q->where('jenis', $jenis));

        $this->applySkripsiFilters($query, $request, 'seminar.skripsi');
        $this->applyDateFilter($query, $request, 'tanggal');

        return $query->latest('tanggal')->paginate($perPage);
    }

    private function lembarPengesahan(Request $request, int $perPage)
    {
        $query = Seminar::with(['skripsi.mahasiswa.prodi', 'lembarPengesahan'])
            ->where('jenis', 'sidang')
            ->whereHas('lembarPengesahan')
            ->whereHas('skripsi');

        $this->applySkripsiFilters($query, $request, 'skripsi');
        // Tanggal lembar pengesahan is in relation, but we can sort by seminar tanggal for simplicity or join
        $this->applyDateFilter($query, $request, 'tanggal');

        return $query->latest('tanggal')->paginate($perPage);
    }

    private function applySkripsiFilters($query, Request $request, string $relation): void
    {
        $this->applyGenderFilter($query, $request, "{$relation}.mahasiswa");

        if ($request->filled('search')) {
            $search = $request->search;
            $query->whereHas($relation, function ($q) use ($search) {
                $q->where('judul', 'like', "%{$search}%")
                    ->orWhereHas('mahasiswa', function ($mhs) use ($search) {
                        $mhs->where('nama', 'like', "%{$search}%")
                            ->orWhere('nim', 'like', "%{$search}%");
                    });
            });
        }

        if ($request->filled('prodi_id')) {
            $query->whereHas("{$relation}.mahasiswa", function ($q) use ($request) {
                $q->where('prodi_id', $request->prodi_id);
            });
        }

        if ($request->filled('fakultas_id')) {
            $query->whereHas("{$relation}.mahasiswa.prodi", function ($q) use ($request) {
                $q->where('fakultas_id', $request->fakultas_id);
            });
        }
    }

    private function applyDateFilter($query, Request $request, string $column): void
    {
        if ($request->filled('tahun_akademik') && str_contains($request->tahun_akademik, '/')) {
            [$startYear, $endYear] = array_map('intval', explode('/', $request->tahun_akademik, 2));
            $query->whereBetween($column, [
                "{$startYear}-08-01",
                "{$endYear}-07-31",
            ]);
        }

        if ($request->filled('tanggal')) {
            $query->whereDate($column, $request->tanggal);
            return;
        }

        if ($request->filled('tanggal_mulai')) {
            $query->whereDate($column, '>=', $request->tanggal_mulai);
        }

        if ($request->filled('tanggal_selesai')) {
            $query->whereDate($column, '<=', $request->tanggal_selesai);
        }
    }

    private function formatItem($item, string $type, array $meta): array
    {
        $skripsi = match ($meta['category']) {
            'sk_tugas', 'nota_bimbingan', 'sk_yudisium' => $item->skripsi,
            'sk_penguji', 'lembar_pengesahan' => $item->skripsi,
            'berita_acara' => $item->seminar?->skripsi,
            'jadwal_ujian' => $item->skripsi,
        };

        $seminar = match ($meta['category']) {
            'sk_penguji', 'jadwal_ujian', 'lembar_pengesahan' => $item,
            'berita_acara' => $item->seminar,
            default => null,
        };

        $nomor = match ($meta['category']) {
            'sk_tugas', 'sk_yudisium' => $item->nomor_sk,
            'nota_bimbingan', 'berita_acara' => $item->nomor,
            'sk_penguji' => $item->nomor_sk_penguji,
            'jadwal_ujian' => $item->ruangan ?: $item->waktu,
            'lembar_pengesahan' => '-',
        };

        $tanggal = match ($meta['category']) {
            'sk_tugas', 'nota_bimbingan', 'sk_yudisium' => $item->tanggal_terbit,
            'berita_acara' => $item->tanggal,
            'sk_penguji', 'jadwal_ujian' => $item->tanggal,
            'lembar_pengesahan' => $item->lembarPengesahan?->tanggal,
        };

        return [
            'id' => $item->id,
            'type' => $type,
            'label' => $meta['label'],
            'nomor' => $nomor,
            'tanggal' => $tanggal,
            'skripsi_id' => $skripsi?->id,
            'seminar_id' => $seminar?->id,
            'mahasiswa' => [
                'nama' => $skripsi?->mahasiswa?->nama,
                'nim' => $skripsi?->mahasiswa?->nim,
            ],
            'prodi' => [
                'nama' => $skripsi?->mahasiswa?->prodi?->nama,
                'kode' => $skripsi?->mahasiswa?->prodi?->kode,
            ],
            'judul' => $skripsi?->judul,
        ];
    }

    private function types(Request $request): array
    {
        return collect(self::TYPES)
            ->map(function ($meta, $key) use ($request) {
                return [
                    'key' => $key,
                    'label' => $meta['label'],
                    'count' => $this->countForType($request, $meta),
                ];
            })
            ->values()
            ->all();
    }

    private function countForType(Request $request, array $meta): int
    {
        $query = match ($meta['category']) {
            'sk_tugas' => SKTugas::query()->whereHas('skripsi'),
            'nota_bimbingan' => NotaBimbingan::query()->whereHas('skripsi'),
            'sk_yudisium' => SKYudisium::query()->whereHas('skripsi'),
            'jadwal_ujian' => Seminar::query()
                ->where('jenis', 'sidang')
                ->whereHas('skripsi'),
            'sk_penguji' => Seminar::query()
                ->where('jenis', $meta['jenis'])
                ->whereHas('penguji')
                ->whereHas('skripsi'),
            'lembar_pengesahan' => Seminar::query()
                ->where('jenis', 'sidang')
                ->whereHas('lembarPengesahan')
                ->whereHas('skripsi'),
            'berita_acara' => BeritaAcara::query()
                ->whereHas('seminar', fn ($q) => $q->where('jenis', $meta['jenis'])),
        };

        $relation = match ($meta['category']) {
            'sk_tugas', 'nota_bimbingan', 'sk_yudisium' => 'skripsi',
            'sk_penguji', 'jadwal_ujian', 'lembar_pengesahan' => 'skripsi',
            'berita_acara' => 'seminar.skripsi',
        };

        $this->applySkripsiFilters($query, $request, $relation);
        $this->applyDateFilter($query, $request, $this->dateColumnForCategory($meta['category']));

        return $query->count();
    }

    private function pdfResponseForItem(Request $request, $item, array $meta)
    {
        $pdfController = app(PdfController::class);

        return match ($meta['category']) {
            'sk_tugas' => $pdfController->skTugas($request, $item->skripsi),
            'nota_bimbingan' => $pdfController->notaBimbingan($request, $item->skripsi),
            'sk_yudisium' => $pdfController->skYudisium($request, $item->skripsi),
            'sk_penguji' => $pdfController->skPenguji($request, $item),
            'berita_acara' => $pdfController->beritaAcaraSeminar($request, $item->seminar),
            'lembar_pengesahan' => $pdfController->lembarPengesahan($request, $item),
            default => throw new RuntimeException('Jenis dokumen tidak tersedia untuk batch download'),
        };
    }

    private function documentFileName(array $item): string
    {
        $nim = $item['mahasiswa']['nim'] ?? 'mahasiswa';
        $names = [
            'sk_tugas' => 'SK_Tugas',
            'nota_bimbingan' => 'Nota_Bimbingan',
            'sk_penguji_sempro' => 'SK_Penguji_Sempro',
            'ba_sempro' => 'Berita_Acara_Sempro',
            'sk_penguji_semhas' => 'SK_Penguji_Semhas',
            'ba_semhas' => 'Berita_Acara_Semhas',
            'sk_penguji_sidang' => 'SK_Penguji_Sidang',
            'ba_sidang' => 'Berita_Acara_Sidang',
            'sk_yudisium' => 'SK_Yudisium',
            'lembar_pengesahan' => 'Lembar_Pengesahan',
        ];

        $base = $names[$item['type']] ?? 'Dokumen';

        return $this->safeFilePart("{$base}_{$nim}") . '.pdf';
    }

    private function uniqueZipEntryName(string $fileName, array &$usedFileNames): string
    {
        $safeName = $this->safeFilePart(pathinfo($fileName, PATHINFO_FILENAME));
        $extension = $this->safeFilePart(pathinfo($fileName, PATHINFO_EXTENSION) ?: 'pdf');
        $candidate = "{$safeName}.{$extension}";
        $counter = 2;

        while (isset($usedFileNames[$candidate])) {
            $candidate = "{$safeName}_{$counter}.{$extension}";
            $counter++;
        }

        $usedFileNames[$candidate] = true;

        return $candidate;
    }

    private function batchZipFileName(Request $request, string $type): string
    {
        $parts = [$this->safeFilePart($type)];
        $filters = [];

        if ($request->filled('tahun_akademik')) {
            $filters[] = 'tahun_' . $request->tahun_akademik;
        }

        if ($request->filled('fakultas_id')) {
            $fakultas = Fakultas::find($request->fakultas_id);
            $filters[] = 'fakultas_' . ($fakultas?->nama_fakultas ?? $request->fakultas_id);
        }

        if ($request->filled('prodi_id')) {
            $prodi = Prodi::find($request->prodi_id);
            $filters[] = 'prodi_' . ($prodi?->nama ?? $request->prodi_id);
        }

        if ($request->filled('tanggal')) {
            $filters[] = 'tanggal_' . $request->tanggal;
        } elseif ($request->filled('tanggal_mulai') || $request->filled('tanggal_selesai')) {
            $filters[] = 'tanggal_' . ($request->tanggal_mulai ?: 'awal') . '_sd_' . ($request->tanggal_selesai ?: 'akhir');
        }

        if ($request->filled('search')) {
            $filters[] = 'cari_' . $request->search;
        }

        $parts[] = $filters
            ? collect($filters)->map(fn ($filter) => $this->safeFilePart($filter))->implode('-')
            : 'semua';

        return implode('-', $parts) . '.zip';
    }

    private function safeFilePart(?string $value, string $fallback = 'file'): string
    {
        $value = Str::ascii((string) ($value ?: $fallback));
        $value = preg_replace('/[^A-Za-z0-9._-]+/', '_', $value) ?: $fallback;
        $value = trim($value, '._-');

        return $value !== '' ? $value : $fallback;
    }

    private function relationForCategory(string $category): string
    {
        return match ($category) {
            'sk_tugas', 'nota_bimbingan', 'sk_yudisium' => 'skripsi',
            'sk_penguji', 'jadwal_ujian', 'lembar_pengesahan' => 'skripsi',
            'berita_acara' => 'seminar.skripsi',
        };
    }

    private function dateColumnForCategory(string $category): string
    {
        return match ($category) {
            'sk_tugas', 'nota_bimbingan', 'sk_yudisium' => 'tanggal_terbit',
            default => 'tanggal',
        };
    }
}
