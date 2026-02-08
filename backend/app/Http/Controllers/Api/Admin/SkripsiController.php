<?php

namespace App\Http\Controllers\Api\Admin;

use App\Http\Controllers\Controller;
use App\Models\Skripsi;
use App\Models\SkripsiHistory;
use Illuminate\Http\Request;

class SkripsiController extends Controller
{
    /**
     * Display a listing of skripsi
     */
    public function index(Request $request)
    {
        $query = Skripsi::with(['mahasiswa.prodi', 'pembimbing.dosen']);

        // Filters
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        if ($request->filled('prodi_id')) {
            $query->whereHas('mahasiswa', function ($q) use ($request) {
                $q->where('prodi_id', $request->prodi_id);
            });
        }

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

        $perPage = $request->get('per_page', 15);
        $skripsi = $query->orderBy('created_at', 'desc')->paginate($perPage);

        return response()->json([
            'success' => true,
            'data' => $skripsi
        ]);
    }

    /**
     * Store a newly created skripsi
     */
    public function store(Request $request)
    {
        $request->validate([
            'mahasiswa_id' => 'required|exists:mahasiswa,id',
            'judul' => 'required|string|max:500',
            'abstrak' => 'nullable|string',
            'kata_kunci' => 'nullable|string',
            'status' => 'nullable|string',
        ]);

        $skripsi = Skripsi::create([
            'mahasiswa_id' => $request->mahasiswa_id,
            'judul' => $request->judul,
            'abstrak' => $request->abstrak,
            'kata_kunci' => $request->kata_kunci,
            'status' => $request->status ?? 'pengajuan',
            'tanggal_daftar' => now(),
            'semester_daftar' => $this->getCurrentSemester(),
            'is_active' => true,
        ]);

        $skripsi->logHistory(null, null, 'Pendaftaran skripsi baru', $request->user());

        return response()->json([
            'success' => true,
            'message' => 'Skripsi berhasil ditambahkan',
            'data' => $skripsi->load(['mahasiswa', 'pembimbing.dosen'])
        ], 201);
    }

    /**
     * Display the specified skripsi
     */
    public function show(Skripsi $skripsi)
    {
        $skripsi->load([
            'mahasiswa.prodi',
            'pembimbing.dosen',
            'bimbingan.dosen',
            'seminar.penguji.dosen',
            'ujian.penguji.dosen',
            'dokumen',
            'nilai',
            'skTugas',
            'notaBimbingan',
            'skYudisium',
            'history.updatedBy'
        ]);

        return response()->json([
            'success' => true,
            'data' => $skripsi
        ]);
    }

    /**
     * Update the specified skripsi
     */
    public function update(Request $request, Skripsi $skripsi)
    {
        $request->validate([
            'judul' => 'sometimes|string|max:500',
            'abstrak' => 'nullable|string',
            'kata_kunci' => 'nullable|string',
            'status' => 'sometimes|string',
            'catatan_admin' => 'nullable|string',
        ]);

        $oldTitle = $skripsi->judul;
        $oldStatus = $skripsi->status;

        $skripsi->fill($request->only([
            'judul', 'abstrak', 'kata_kunci', 'status', 'catatan_admin'
        ]));

        // Update progress based on status
        $skripsi->progress_percentage = $this->calculateProgress($skripsi->status);

        $skripsi->save();

        // Log history if title or status changed
        if ($oldTitle !== $skripsi->judul || $oldStatus !== $skripsi->status) {
            $skripsi->logHistory($oldTitle, $oldStatus, $request->alasan, $request->user());
        }

        return response()->json([
            'success' => true,
            'message' => 'Skripsi berhasil diperbarui',
            'data' => $skripsi->load(['mahasiswa', 'pembimbing.dosen'])
        ]);
    }

    /**
     * Remove the specified skripsi
     */
    public function destroy(Skripsi $skripsi)
    {
        $skripsi->is_active = false;
        $skripsi->save();

        return response()->json([
            'success' => true,
            'message' => 'Skripsi berhasil dihapus'
        ]);
    }

    /**
     * Get current semester string
     */
    private function getCurrentSemester(): string
    {
        $month = now()->month;
        $year = now()->year;

        if ($month >= 2 && $month <= 7) {
            return "Genap " . ($year - 1) . "/" . $year;
        } else {
            $startYear = $month >= 8 ? $year : $year - 1;
            return "Ganjil " . $startYear . "/" . ($startYear + 1);
        }
    }

    /**
     * Calculate progress percentage based on status
     */
    private function calculateProgress(string $status): int
    {
        $progressMap = [
            'draft' => 0,
            'pengajuan' => 5,
            'disetujui' => 10,
            'ditolak' => 0,
            'proposal' => 15,
            'sempro' => 25,
            'bimbingan' => 50,
            'semhas' => 70,
            'sidang' => 85,
            'revisi' => 90,
            'lulus' => 100,
        ];

        return $progressMap[$status] ?? 0;
    }
}
