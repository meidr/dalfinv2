<?php

namespace App\Http\Controllers\Api\Admin;

use App\Http\Controllers\Controller;
use App\Models\Configuration;
use App\Models\MentorSempro;
use App\Models\Skripsi;
use App\Models\Dosen;
use App\Traits\GenderFilterable;
use Illuminate\Http\Request;

class MentorSemproController extends Controller
{
    use GenderFilterable;

    /**
     * Display skripsi waiting for or having mentor assignment
     */
    public function index(Request $request)
    {
        $query = Skripsi::with(['mahasiswa.prodi', 'mentorSempro.dosen'])
            ->where('is_active', true)
            ->whereIn('status', ['disetujui', 'penentuan_mentor', 'mentor', 'proposal', 'sempro']);

        // Gender-based filtering
        $this->applyGenderFilter($query, $request);

        // Filter by mentor status: sudah / belum
        if ($request->filled('mentor_status')) {
            if ($request->mentor_status === 'sudah') {
                $query->whereHas('mentorSempro');
            } elseif ($request->mentor_status === 'belum') {
                $query->whereDoesntHave('mentorSempro');
            }
        }

        // Search by nama, nim, or judul
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('judul', 'like', "%{$search}%")
                    ->orWhereHas('mahasiswa', function ($q2) use ($search) {
                        $q2->where('nama', 'like', "%{$search}%")
                            ->orWhere('nim', 'like', "%{$search}%");
                    });
            });
        }

        if ($request->filled('prodi_id')) {
            $query->whereHas('mahasiswa', function ($q) use ($request) {
                $q->where('prodi_id', $request->prodi_id);
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
     * Get available dosen for mentor assignment
     */
    public function availableDosen(Request $request)
    {
        // Get global kuota default for mentor
        $globalConfig = Configuration::where('key', 'kuota_mentor_default')->first();
        $globalKuota = $globalConfig ? ($globalConfig->value['kuota'] ?? 10) : 10;

        $query = Dosen::with('prodi')
            ->where('is_active', true)
            ->withCount(['mentorSempro as current_mentor' => function ($q) {
                $q->where('is_active', true);
            }]);

        if ($request->filled('prodi_id')) {
            $query->where('prodi_id', $request->prodi_id);
        }

        if ($request->filled('bidang_keahlian')) {
            $query->where('bidang_keahlian', 'like', "%{$request->bidang_keahlian}%");
        }

        $dosen = $query->orderBy('nama', 'asc')->get()->map(function ($d) use ($globalKuota) {
            $kuota = $d->kuota_mentor ?: $globalKuota;
            return [
                'id' => $d->id,
                'nama' => $d->nama,
                'nama_lengkap' => $d->full_name,
                'bidang_keahlian' => $d->bidang_keahlian,
                'kuota_mentor' => $kuota,
                'current_mentor' => $d->current_mentor,
                'is_available' => $d->current_mentor < $kuota,
            ];
        });

        // Extract unique bidang keahlian for filter
        $bidangList = $dosen->pluck('bidang_keahlian')
            ->filter()
            ->flatMap(fn($b) => explode(',', $b))
            ->map(fn($b) => trim($b))
            ->unique()
            ->sort()
            ->values();

        return response()->json([
            'success' => true,
            'data' => $dosen,
            'bidang_list' => $bidangList,
        ]);
    }

    /**
     * Assign mentor to skripsi
     */
    public function store(Request $request)
    {
        $request->validate([
            'skripsi_id' => 'required|exists:skripsi,id',
            'mentor_1_id' => 'required|exists:dosen,id',
            'mentor_2_id' => 'nullable|exists:dosen,id|different:mentor_1_id',
        ]);

        $skripsi = Skripsi::with('mahasiswa')->findOrFail($request->skripsi_id);

        // Check kuota for mentor 1
        $globalConfig = Configuration::where('key', 'kuota_mentor_default')->first();
        $globalKuota = $globalConfig ? ($globalConfig->value['kuota'] ?? 10) : 10;

        $dosen1 = Dosen::withCount(['mentorSempro as current_mentor' => function ($q) {
            $q->where('is_active', true);
        }])->findOrFail($request->mentor_1_id);
        $kuota1 = $dosen1->kuota_mentor ?: $globalKuota;

        // Only check quota if this is a NEW assignment
        $existingMentor1 = MentorSempro::where('skripsi_id', $skripsi->id)
            ->where('jenis', 'mentor_1')
            ->where('dosen_id', $request->mentor_1_id)
            ->first();
        if (!$existingMentor1 && $dosen1->current_mentor >= $kuota1) {
            return response()->json([
                'success' => false,
                'message' => 'Kuota mentor dosen ' . $dosen1->nama . ' sudah penuh (' . $dosen1->current_mentor . '/' . $kuota1 . ')',
            ], 422);
        }

        if ($request->mentor_2_id) {
            $dosen2 = Dosen::withCount(['mentorSempro as current_mentor' => function ($q) {
                $q->where('is_active', true);
            }])->findOrFail($request->mentor_2_id);
            $kuota2 = $dosen2->kuota_mentor ?: $globalKuota;

            $existingMentor2 = MentorSempro::where('skripsi_id', $skripsi->id)
                ->where('jenis', 'mentor_2')
                ->where('dosen_id', $request->mentor_2_id)
                ->first();
            if (!$existingMentor2 && $dosen2->current_mentor >= $kuota2) {
                return response()->json([
                    'success' => false,
                    'message' => 'Kuota mentor dosen ' . $dosen2->nama . ' sudah penuh (' . $dosen2->current_mentor . '/' . $kuota2 . ')',
                ], 422);
            }
        }

        // Create mentor 1
        MentorSempro::updateOrCreate(
            ['skripsi_id' => $skripsi->id, 'jenis' => 'mentor_1'],
            [
                'dosen_id' => $request->mentor_1_id,
                'tanggal_penetapan' => now(),
                'is_active' => true,
            ]
        );

        // Create mentor 2 if provided
        if ($request->mentor_2_id) {
            MentorSempro::updateOrCreate(
                ['skripsi_id' => $skripsi->id, 'jenis' => 'mentor_2'],
                [
                    'dosen_id' => $request->mentor_2_id,
                    'tanggal_penetapan' => now(),
                    'is_active' => true,
                ]
            );
        } else {
            // Remove mentor 2 if not provided
            MentorSempro::where('skripsi_id', $skripsi->id)
                ->where('jenis', 'mentor_2')
                ->delete();
        }

        // Update skripsi status to mentor when mentor assigned
        if (in_array($skripsi->status, ['disetujui', 'penentuan_mentor'])) {
            $skripsi->status = 'mentor';
            $skripsi->progress_percentage = 14;
            $skripsi->save();
        }

        return response()->json([
            'success' => true,
            'message' => 'Mentor sempro berhasil ditetapkan',
            'data' => $skripsi->load(['mentorSempro.dosen'])
        ]);
    }

    /**
     * Update mentor assignment
     */
    public function update(Request $request, MentorSempro $mentorSempro)
    {
        $request->validate([
            'dosen_id' => 'required|exists:dosen,id',
        ]);

        $mentorSempro->dosen_id = $request->dosen_id;
        $mentorSempro->tanggal_penetapan = now();
        $mentorSempro->save();

        return response()->json([
            'success' => true,
            'message' => 'Mentor sempro berhasil diperbarui',
            'data' => $mentorSempro->load('dosen')
        ]);
    }

    /**
     * Remove mentor
     */
    public function destroy(MentorSempro $mentorSempro)
    {
        $skripsi = $mentorSempro->skripsi;
        $mentorSempro->delete();

        // Check if there are remaining mentors
        $remainingMentors = MentorSempro::where('skripsi_id', $skripsi->id)->count();

        // If no mentors left, revert status to penentuan_mentor
        if ($remainingMentors === 0 && in_array($skripsi->status, ['mentor'])) {
            $skripsi->status = 'penentuan_mentor';
            $skripsi->progress_percentage = 12;
            $skripsi->save();
        }

        return response()->json([
            'success' => true,
            'message' => 'Mentor sempro berhasil dihapus'
        ]);
    }
}
