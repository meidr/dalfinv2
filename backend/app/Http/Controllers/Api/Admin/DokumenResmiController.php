<?php

namespace App\Http\Controllers\Api\Admin;

use App\Http\Controllers\Controller;
use App\Models\BeritaAcara;
use App\Models\NotaBimbingan;
use App\Models\Seminar;
use App\Models\SKTugas;
use App\Models\SKYudisium;
use App\Traits\GenderFilterable;
use Illuminate\Http\Request;

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
            'sk_penguji' => $this->skPenguji($request, $meta['jenis'], $perPage),
            'berita_acara' => $this->beritaAcara($request, $meta['jenis'], $perPage),
        };

        $paginator->getCollection()->transform(fn ($item) => $this->formatItem($item, $type, $meta));

        return response()->json([
            'success' => true,
            'types' => $this->types($request),
            'data' => $paginator,
        ]);
    }

    private function skTugas(Request $request, int $perPage)
    {
        $query = SKTugas::with('skripsi.mahasiswa.prodi')
            ->whereHas('skripsi');

        $this->applySkripsiFilters($query, $request, 'skripsi');

        return $query->latest('tanggal_terbit')->paginate($perPage);
    }

    private function notaBimbingan(Request $request, int $perPage)
    {
        $query = NotaBimbingan::with('skripsi.mahasiswa.prodi')
            ->whereHas('skripsi');

        $this->applySkripsiFilters($query, $request, 'skripsi');

        return $query->latest('tanggal_terbit')->paginate($perPage);
    }

    private function skYudisium(Request $request, int $perPage)
    {
        $query = SKYudisium::with('skripsi.mahasiswa.prodi')
            ->whereHas('skripsi');

        $this->applySkripsiFilters($query, $request, 'skripsi');

        return $query->latest('tanggal_terbit')->paginate($perPage);
    }

    private function skPenguji(Request $request, string $jenis, int $perPage)
    {
        $query = Seminar::with(['skripsi.mahasiswa.prodi', 'penguji'])
            ->where('jenis', $jenis)
            ->whereHas('penguji')
            ->whereHas('skripsi');

        $this->applySkripsiFilters($query, $request, 'skripsi');

        return $query->latest('tanggal')->paginate($perPage);
    }

    private function beritaAcara(Request $request, string $jenis, int $perPage)
    {
        $query = BeritaAcara::with('seminar.skripsi.mahasiswa.prodi')
            ->whereHas('seminar', fn ($q) => $q->where('jenis', $jenis));

        $this->applySkripsiFilters($query, $request, 'seminar.skripsi');

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
    }

    private function formatItem($item, string $type, array $meta): array
    {
        $skripsi = match ($meta['category']) {
            'sk_tugas', 'nota_bimbingan', 'sk_yudisium' => $item->skripsi,
            'sk_penguji' => $item->skripsi,
            'berita_acara' => $item->seminar?->skripsi,
        };

        $seminar = match ($meta['category']) {
            'sk_penguji' => $item,
            'berita_acara' => $item->seminar,
            default => null,
        };

        $nomor = match ($meta['category']) {
            'sk_tugas', 'sk_yudisium' => $item->nomor_sk,
            'nota_bimbingan', 'berita_acara' => $item->nomor,
            'sk_penguji' => $item->nomor_sk_penguji,
        };

        $tanggal = match ($meta['category']) {
            'sk_tugas', 'nota_bimbingan', 'sk_yudisium' => $item->tanggal_terbit,
            'berita_acara' => $item->tanggal,
            'sk_penguji' => $item->tanggal,
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
            'sk_penguji' => Seminar::query()
                ->where('jenis', $meta['jenis'])
                ->whereHas('penguji')
                ->whereHas('skripsi'),
            'berita_acara' => BeritaAcara::query()
                ->whereHas('seminar', fn ($q) => $q->where('jenis', $meta['jenis'])),
        };

        $relation = match ($meta['category']) {
            'sk_tugas', 'nota_bimbingan', 'sk_yudisium' => 'skripsi',
            'sk_penguji' => 'skripsi',
            'berita_acara' => 'seminar.skripsi',
        };

        $this->applySkripsiFilters($query, $request, $relation);

        return $query->count();
    }
}
