<?php

namespace App\Http\Controllers\Api\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Seminar;
use App\Models\Skripsi;
use App\Models\Penguji;
use App\Models\Pembimbing;

class UjianController extends Controller
{
    public function index(Request $request)
    {
        $query = Seminar::with([
            'skripsi.mahasiswa.prodi',
            'skripsi.pembimbing.dosen',
            'penguji.dosen'
        ])->where('jenis', 'sidang');

        if ($request->filled('search')) {
            $search = $request->search;
            $query->whereHas('skripsi.mahasiswa', function ($q) use ($search) {
                $q->where('nama', 'like', "%{$search}%")
                    ->orWhere('nim', 'like', "%{$search}%");
            });
        }

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        // Filter by prodi
        if ($request->filled('prodi_id')) {
            $query->whereHas('skripsi.mahasiswa', function ($q) use ($request) {
                $q->where('prodi_id', $request->prodi_id);
            });
        }

        // Filter by tahun akademik (year of exam date)
        if ($request->filled('tahun_akademik')) {
            $tahun = $request->tahun_akademik;
            // tahun_akademik format: "2024/2025" → filter dates between Aug 2024 - Jul 2025
            if (str_contains($tahun, '/')) {
                $parts = explode('/', $tahun);
                $startYear = (int) $parts[0];
                $endYear = (int) $parts[1];
                $query->where(function ($q) use ($startYear, $endYear) {
                    $q->whereBetween('tanggal', [
                        "{$startYear}-08-01",
                        "{$endYear}-07-31"
                    ]);
                });
            }
        }

        // Filter by semester (ganjil = Aug-Jan, genap = Feb-Jul)
        if ($request->filled('semester')) {
            $sem = $request->semester;
            if ($sem === 'ganjil') {
                $query->where(function ($q) {
                    $q->whereMonth('tanggal', '>=', 8)
                        ->orWhereMonth('tanggal', '<=', 1);
                });
            } elseif ($sem === 'genap') {
                $query->whereMonth('tanggal', '>=', 2)
                    ->whereMonth('tanggal', '<=', 7);
            }
        }

        $ujian = $query->orderBy('tanggal', 'desc')->paginate(10);

        // Add grade to each item
        $ujian->getCollection()->transform(function ($item) {
            $item->grade = $item->nilai ? $this->getGrade($item->nilai) : null;
            return $item;
        });

        // Stats
        $stats = [
            'terjadwal' => Seminar::where('jenis', 'sidang')->where('status', 'terjadwal')->count(),
            'sedang_ujian' => Seminar::where('jenis', 'sidang')->whereIn('status', ['berlangsung', 'pending'])->count(),
            'selesai' => Seminar::where('jenis', 'sidang')->where('status', 'selesai')->count(),
            'total' => Seminar::where('jenis', 'sidang')->count(),
        ];

        return response()->json([
            'success' => true,
            'data' => $ujian,
            'stats' => $stats
        ]);
    }

    public function show($id)
    {
        $ujian = Seminar::with([
            'skripsi.mahasiswa.prodi',
            'skripsi.pembimbing.dosen',
            'penguji.dosen',
            'beritaAcara'
        ])->where('jenis', 'sidang')->findOrFail($id);

        return response()->json([
            'success' => true,
            'data' => $this->formatUjianResponse($ujian)
        ]);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'skripsi_id' => 'required|exists:skripsi,id',
            'tanggal' => 'required|date',
            'waktu' => 'required|string',
            'ruangan' => 'required|string',
            'penguji' => 'sometimes|array|max:3',
            'penguji.*.dosen_id' => 'required|exists:dosen,id',
            'penguji.*.peran' => 'required|in:ketua,penguji_1,penguji_2',
        ]);

        // Validate penguji not pembimbing
        if (!empty($validated['penguji'])) {
            $pembimbingDosenIds = Pembimbing::where('skripsi_id', $validated['skripsi_id'])
                ->pluck('dosen_id')->toArray();

            foreach ($validated['penguji'] as $p) {
                if (in_array($p['dosen_id'], $pembimbingDosenIds)) {
                    return response()->json([
                        'success' => false,
                        'message' => 'Dosen pembimbing tidak bisa menjadi dosen penguji'
                    ], 422);
                }
            }
        }

        $ujian = Seminar::create([
            'skripsi_id' => $validated['skripsi_id'],
            'jenis' => 'sidang',
            'tanggal' => $validated['tanggal'],
            'waktu' => $validated['waktu'],
            'ruangan' => $validated['ruangan'],
            'status' => 'pending',
        ]);

        // Create penguji
        if (!empty($validated['penguji'])) {
            foreach ($validated['penguji'] as $p) {
                Penguji::create([
                    'seminar_id' => $ujian->id,
                    'dosen_id' => $p['dosen_id'],
                    'peran' => $p['peran'],
                ]);
            }
        }

        $ujian->load('penguji.dosen');

        // Auto-update skripsi status to 'sidang' if currently 'pengajuan_sidang'
        $skripsi = Skripsi::find($validated['skripsi_id']);
        if ($skripsi && $skripsi->status === 'pengajuan_sidang') {
            $skripsi->update(['status' => 'sidang', 'progress_percentage' => 85]);
        }

        return response()->json([
            'success' => true,
            'message' => 'Ujian berhasil dijadwalkan',
            'data' => $ujian
        ], 201);
    }

    public function update(Request $request, $id)
    {
        $ujian = Seminar::where('jenis', 'sidang')->findOrFail($id);

        $validated = $request->validate([
            'tanggal' => 'sometimes|date',
            'waktu' => 'sometimes|string',
            'ruangan' => 'sometimes|string',
            'status' => 'sometimes|string|in:pending,selesai,batal,terjadwal',
            'hasil' => 'sometimes|string|in:lulus,lulus_revisi,tidak_lulus',
            'catatan' => 'nullable|string',
            'penguji' => 'sometimes|array|max:3',
            'penguji.*.dosen_id' => 'required|exists:dosen,id',
            'penguji.*.peran' => 'required|in:ketua,penguji_1,penguji_2',
            'penguji.*.nilai_mt' => 'nullable|numeric|min:0|max:100',
            'penguji.*.nilai_ms' => 'nullable|numeric|min:0|max:100',
            'penguji.*.nilai_pm' => 'nullable|numeric|min:0|max:100',
            'penguji.*.nilai_pi' => 'nullable|numeric|min:0|max:100',
            'penguji.*.catatan' => 'nullable|string',
        ]);

        // Validate penguji not pembimbing
        if (!empty($validated['penguji'])) {
            $pembimbingDosenIds = Pembimbing::where('skripsi_id', $ujian->skripsi_id)
                ->pluck('dosen_id')->toArray();

            foreach ($validated['penguji'] as $p) {
                if (in_array($p['dosen_id'], $pembimbingDosenIds)) {
                    return response()->json([
                        'success' => false,
                        'message' => 'Dosen pembimbing tidak bisa menjadi dosen penguji'
                    ], 422);
                }
            }
        }

        // Update seminar schedule/status fields
        $ujianFields = collect($validated)->only([
            'tanggal',
            'waktu',
            'ruangan',
            'status',
            'hasil',
            'catatan'
        ])->toArray();

        if (!empty($ujianFields)) {
            $ujian->update($ujianFields);
        }

        // Auto-update skripsi status when admin manually sets hasil (without penguji recalc)
        if (isset($validated['hasil']) && !isset($validated['penguji'])) {
            $skripsi = $ujian->skripsi;
            if ($skripsi && in_array($skripsi->status, ['sidang', 'pengajuan_sidang', 'bimbingan'])) {
                if ($validated['hasil'] === 'lulus') {
                    $skripsi->update([
                        'status' => 'lulus',
                        'progress_percentage' => 100,
                    ]);
                } elseif ($validated['hasil'] === 'lulus_revisi') {
                    $skripsi->update([
                        'status' => 'revisi',
                        'progress_percentage' => 90,
                    ]);
                }
            }
        }

        // Sync penguji if provided (with component scores)
        if (isset($validated['penguji'])) {
            Penguji::where('seminar_id', $ujian->id)->delete();

            foreach ($validated['penguji'] as $p) {
                $mt = $p['nilai_mt'] ?? null;
                $ms = $p['nilai_ms'] ?? null;
                $pm = $p['nilai_pm'] ?? null;
                $pi = $p['nilai_pi'] ?? null;

                // Auto-calculate per-penguji average from filled components
                $components = array_filter([$mt, $ms, $pm, $pi], fn($v) => $v !== null);
                $pengujiAvg = count($components) === 4
                    ? round(array_sum($components) / 4, 2)
                    : null;

                Penguji::create([
                    'seminar_id' => $ujian->id,
                    'dosen_id' => $p['dosen_id'],
                    'peran' => $p['peran'],
                    'nilai_mt' => $mt,
                    'nilai_ms' => $ms,
                    'nilai_pm' => $pm,
                    'nilai_pi' => $pi,
                    'nilai' => $pengujiAvg,
                    'catatan' => $p['catatan'] ?? null,
                ]);
            }

            // Auto-calculate final average from penguji averages
            $this->recalculateNilai($ujian);
        }

        $ujian->load(['penguji.dosen', 'skripsi.mahasiswa']);

        return response()->json([
            'success' => true,
            'message' => 'Data ujian berhasil diperbarui',
            'data' => $this->formatUjianResponse($ujian)
        ]);
    }

    /**
     * Recalculate the average score from all penguji
     */
    private function recalculateNilai(Seminar $ujian)
    {
        $pengujiScores = Penguji::where('seminar_id', $ujian->id)
            ->whereNotNull('nilai')
            ->pluck('nilai');

        if ($pengujiScores->count() > 0) {
            $average = round($pengujiScores->avg(), 2);

            $updateData = ['nilai' => $average];

            // Auto-set hasil if all penguji have scored
            $totalPenguji = Penguji::where('seminar_id', $ujian->id)->count();
            if ($pengujiScores->count() === $totalPenguji) {
                if ($average >= 55) {
                    $updateData['hasil'] = $ujian->hasil ?: 'lulus';
                } else {
                    $updateData['hasil'] = 'tidak_lulus';
                }
                $updateData['status'] = 'selesai';

                // Auto-update skripsi status based on hasil
                $finalHasil = $updateData['hasil'];
                $skripsi = $ujian->skripsi;
                if ($skripsi && in_array($skripsi->status, ['sidang', 'pengajuan_sidang', 'bimbingan'])) {
                    if ($finalHasil === 'lulus') {
                        $skripsi->update([
                            'status' => 'lulus',
                            'progress_percentage' => 100,
                        ]);
                    } elseif ($finalHasil === 'lulus_revisi') {
                        $skripsi->update([
                            'status' => 'revisi',
                            'progress_percentage' => 90,
                        ]);
                    }
                }
            }

            $ujian->update($updateData);
        }
    }

    /**
     * Get letter grade from numeric score
     */
    private function getGrade($nilai): string
    {
        if ($nilai >= 85) return 'A';
        if ($nilai >= 80) return 'B+';
        if ($nilai >= 70) return 'B';
        if ($nilai >= 65) return 'C+';
        if ($nilai >= 55) return 'C';
        return 'D';
    }

    /**
     * Format ujian response with computed grade
     */
    private function formatUjianResponse(Seminar $ujian): array
    {
        $data = $ujian->toArray();
        $data['grade'] = $ujian->nilai ? $this->getGrade($ujian->nilai) : null;
        return $data;
    }

    /**
     * Get available dosen (excluding pembimbing for given skripsi)
     */
    public function availablePenguji(Request $request, $ujianId)
    {
        $ujian = Seminar::where('jenis', 'sidang')->findOrFail($ujianId);

        $pembimbingDosenIds = Pembimbing::where('skripsi_id', $ujian->skripsi_id)
            ->pluck('dosen_id')->toArray();

        $query = \App\Models\Dosen::whereNotIn('id', $pembimbingDosenIds);

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('nama', 'like', "%{$search}%")
                    ->orWhere('nip', 'like', "%{$search}%");
            });
        }

        $dosen = $query->orderBy('nama')->limit(20)->get()->map(function ($d) {
            return [
                'id' => $d->id,
                'nama' => $d->nama,
                'full_name' => $d->full_name,
                'nip' => $d->nip,
            ];
        });

        return response()->json([
            'success' => true,
            'data' => $dosen
        ]);
    }
}
