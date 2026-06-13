<?php

namespace App\Http\Controllers\Api\Admin;

use App\Http\Controllers\Controller;
use App\Models\Skripsi;
use App\Models\SkripsiSimilarity;
use App\Services\SimilarityService;
use App\Traits\GenderFilterable;
use Illuminate\Http\Request;

class SimilarityController extends Controller
{
    use GenderFilterable;

    /**
     * List skripsi with their highest similarity scores.
     * Used for the "Similarity Judul" tab in DataSkripsi page.
     */
    public function index(Request $request)
    {
        $query = Skripsi::with(['mahasiswa.prodi', 'tahunAkademik'])
            ->withCount(['similarities as similar_count' => function ($q) {
                $q->where('similarity_score', '>=', 70);
            }])
            ->withMax('similarities as max_similarity', 'similarity_score');

        // Gender-based filtering
        $this->applyGenderFilter($query, $request);

        // Filter: only show items with similarity >= 70%
        if ($request->boolean('has_similarity', false)) {
            $query->whereHas('similarities', function ($q) {
                $q->where('similarity_score', '>=', 70);
            });
        }

        // Filter by status
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        // Search
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('judul', 'like', "%{$search}%")
                    ->orWhereHas('mahasiswa', function ($mhs) use ($search) {
                        $mhs->where('nama', 'like', "%{$search}%")
                            ->orWhere('nim', 'like', "%{$search}%");
                    });
            });
        }

        // Sorting
        $sortBy = $request->get('sort_by', 'max_similarity');
        $sortOrder = $request->get('sort_order', 'desc');

        if ($sortBy === 'max_similarity') {
            $query->orderByRaw('COALESCE((SELECT MAX(similarity_score) FROM skripsi_similarities WHERE skripsi_similarities.skripsi_id = skripsi.id), 0) ' . ($sortOrder === 'asc' ? 'ASC' : 'DESC'));
        } elseif ($sortBy === 'mahasiswa_nama') {
            $query->join('mahasiswa', 'skripsi.mahasiswa_id', '=', 'mahasiswa.id')
                ->orderBy('mahasiswa.nama', $sortOrder)
                ->select('skripsi.*');
        } else {
            $allowedSorts = ['created_at', 'judul', 'status'];
            if (in_array($sortBy, $allowedSorts)) {
                $query->orderBy($sortBy, $sortOrder);
            } else {
                $query->orderByRaw('COALESCE((SELECT MAX(similarity_score) FROM skripsi_similarities WHERE skripsi_similarities.skripsi_id = skripsi.id), 0) DESC');
            }
        }

        $perPage = $request->get('per_page', 15);
        $data = $query->paginate($perPage);

        // Attach max_similarity and similar_count to each item
        $data->getCollection()->transform(function ($item) {
            $item->max_similarity = round((float) ($item->max_similarity ?? 0), 2);
            $item->similar_count = (int) ($item->similar_count ?? 0);
            $item->similarity_category = SkripsiSimilarity::categorize($item->max_similarity);
            return $item;
        });

        return response()->json([
            'success' => true,
            'data' => $data,
        ]);
    }

    /**
     * Show detailed similarity results for a specific skripsi.
     */
    public function show(Skripsi $skripsi)
    {
        $similarities = SkripsiSimilarity::where('skripsi_id', $skripsi->id)
            ->where('similarity_score', '>=', 30)
            ->with(['comparedSkripsi.mahasiswa.prodi', 'comparedSkripsi.tahunAkademik'])
            ->orderByDesc('similarity_score')
            ->get()
            ->map(function ($sim) {
                return [
                    'id' => $sim->id,
                    'similarity_score' => round($sim->similarity_score, 2),
                    'category' => $sim->category,
                    'compared_skripsi' => [
                        'id' => $sim->comparedSkripsi->id,
                        'judul' => $sim->comparedSkripsi->judul,
                        'status' => $sim->comparedSkripsi->status,
                        'mahasiswa' => [
                            'nama' => $sim->comparedSkripsi->mahasiswa->nama ?? '-',
                            'nim' => $sim->comparedSkripsi->mahasiswa->nim ?? '-',
                            'prodi' => $sim->comparedSkripsi->mahasiswa->prodi->nama ?? '-',
                        ],
                        'tahun_akademik' => $sim->comparedSkripsi->tahunAkademik->name ?? '-',
                        'created_at' => $sim->comparedSkripsi->created_at,
                    ],
                ];
            });

        return response()->json([
            'success' => true,
            'data' => [
                'skripsi' => $skripsi->load(['mahasiswa.prodi', 'tahunAkademik']),
                'similarities' => $similarities,
            ],
        ]);
    }

    /**
     * Recalculate similarity for a specific skripsi
     */
    public function recalculate(Skripsi $skripsi)
    {
        $service = new SimilarityService();
        $service->calculateForSkripsi($skripsi);

        return response()->json([
            'success' => true,
            'message' => 'Similarity berhasil dihitung ulang untuk judul ini.',
        ]);
    }

    /**
     * Recalculate all similarity (admin only, for initialization)
     */
    public function recalculateAll()
    {
        $service = new SimilarityService();
        $pairs = $service->recalculateAll();

        return response()->json([
            'success' => true,
            'message' => "Similarity berhasil dihitung ulang. {$pairs} pasangan judul ditemukan.",
        ]);
    }
}
