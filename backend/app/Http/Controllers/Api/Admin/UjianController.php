<?php

namespace App\Http\Controllers\Api\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Seminar;
use App\Models\Skripsi;
use App\Models\Penguji;
use App\Models\Pembimbing;
use App\Models\SkripsiHistory;

class UjianController extends Controller
{
    /**
     * Apply gender-based filtering.
     * Staff/admin only see mahasiswa of same gender. Super admin sees all.
     */
    private function applyMahasiswaGenderFilter($query, Request $request, string $relation = 'skripsi.mahasiswa')
    {
        $user = $request->user();
        if ($user->role !== 'super_admin' && $user->jenis_kelamin) {
            $gender = $user->jenis_kelamin;
            $query->whereHas($relation, function ($q) use ($gender) {
                $q->where('jenis_kelamin', $gender);
            });
        }
        return $query;
    }

    public function index(Request $request)
    {
        $query = Seminar::with([
            'skripsi.mahasiswa.prodi',
            'skripsi.pembimbing.dosen',
            'penguji.dosen'
        ])->where('jenis', 'sidang');

        // Gender-based filtering
        $this->applyMahasiswaGenderFilter($query, $request);

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

        // Stats (also filtered by gender)
        $user = $request->user();
        $genderFilter = ($user->role !== 'super_admin' && $user->jenis_kelamin) ? $user->jenis_kelamin : null;

        $statsBase = function () use ($genderFilter) {
            $q = Seminar::where('jenis', 'sidang');
            if ($genderFilter) {
                $q->whereHas('skripsi.mahasiswa', function ($mq) use ($genderFilter) {
                    $mq->where('jenis_kelamin', $genderFilter);
                });
            }
            return $q;
        };

        $stats = [
            'terjadwal' => $statsBase()->where('status', 'terjadwal')->count(),
            'sedang_ujian' => $statsBase()->whereIn('status', ['berlangsung', 'pending'])->count(),
            'selesai' => $statsBase()->where('status', 'selesai')->count(),
            'total' => $statsBase()->count(),
        ];

        return response()->json([
            'success' => true,
            'data' => $ujian,
            'stats' => $stats
        ]);
    }

    /**
     * List skripsi eligible for sidang (pengajuan_sidang approved by dosen)
     * Follows the SeminarController.index pattern
     */
    public function eligible(Request $request)
    {
        $query = Skripsi::with(['mahasiswa.prodi', 'pembimbing.dosen', 'seminar' => function ($q) {
            $q->where('jenis', 'sidang')->with('penguji.dosen')->latest('tanggal');
        }])
            ->where('is_active', true)
            ->whereIn('status', ['pengajuan_sidang_acc', 'sidang', 'revisi', 'lulus']);

        // Gender-based filtering
        $this->applyMahasiswaGenderFilter($query, $request, 'mahasiswa');

        // Search filter
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('judul', 'like', "%{$search}%")
                    ->orWhereHas('mahasiswa', function ($mq) use ($search) {
                        $mq->where('nama', 'like', "%{$search}%")
                            ->orWhere('nim', 'like', "%{$search}%");
                    });
            });
        }

        // Jadwal filter: terjadwal or belum
        if ($request->filled('jadwal')) {
            if ($request->jadwal === 'terjadwal') {
                $query->whereHas('seminar', function ($q) {
                    $q->where('jenis', 'sidang');
                });
            } elseif ($request->jadwal === 'belum') {
                $query->whereDoesntHave('seminar', function ($q) {
                    $q->where('jenis', 'sidang');
                });
            }
        }

        // Filter by prodi
        if ($request->filled('prodi_id')) {
            $query->whereHas('mahasiswa', function ($q) use ($request) {
                $q->where('prodi_id', $request->prodi_id);
            });
        }

        // Filter by fakultas
        if ($request->filled('fakultas_id')) {
            $query->whereHas('mahasiswa.prodi', function ($q) use ($request) {
                $q->where('fakultas_id', $request->fakultas_id);
            });
        }

        // Filter by tahun akademik (e.g. "2024/2025")
        if ($request->filled('tahun_akademik')) {
            $tahun = $request->tahun_akademik;
            if (str_contains($tahun, '/')) {
                $parts = explode('/', $tahun);
                $startYear = (int) $parts[0];
                $endYear = (int) $parts[1];
                $query->whereHas('seminar', function ($q) use ($startYear, $endYear) {
                    $q->where('jenis', 'sidang')
                        ->whereBetween('tanggal', ["{$startYear}-08-01", "{$endYear}-07-31"]);
                });
            }
        }

        // Filter by semester
        if ($request->filled('semester')) {
            $sem = $request->semester;
            $query->whereHas('seminar', function ($q) use ($sem) {
                $q->where('jenis', 'sidang');
                if ($sem === 'ganjil') {
                    $q->where(function ($sq) {
                        $sq->whereMonth('tanggal', '>=', 8)
                            ->orWhereMonth('tanggal', '<=', 1);
                    });
                } elseif ($sem === 'genap') {
                    $q->whereMonth('tanggal', '>=', 2)
                        ->whereMonth('tanggal', '<=', 7);
                }
            });
        }

        // Filter by pembimbing name
        if ($request->filled('pembimbing')) {
            $pembimbing = $request->pembimbing;
            $query->whereHas('pembimbing.dosen', function ($q) use ($pembimbing) {
                $q->where(function ($sq) use ($pembimbing) {
                    $sq->where('nama', 'like', "%{$pembimbing}%")
                        ->orWhere('gelar_depan', 'like', "%{$pembimbing}%")
                        ->orWhere('gelar_belakang', 'like', "%{$pembimbing}%");
                });
            });
        }

        // Filter by penguji name
        if ($request->filled('penguji')) {
            $penguji = $request->penguji;
            $query->whereHas('seminar.penguji.dosen', function ($q) use ($penguji) {
                $q->where(function ($sq) use ($penguji) {
                    $sq->where('nama', 'like', "%{$penguji}%")
                        ->orWhere('gelar_depan', 'like', "%{$penguji}%")
                        ->orWhere('gelar_belakang', 'like', "%{$penguji}%");
                });
            });
        }

        $perPage = $request->get('per_page', 15);
        $skripsiList = $query->orderBy('created_at', 'desc')->paginate($perPage);

        // Add computed fields
        $skripsiList->getCollection()->transform(function ($skripsi) {
            $seminarSidang = $skripsi->seminar->where('jenis', 'sidang')->first();
            $skripsi->sidang_seminar = $seminarSidang;
            $skripsi->is_scheduled = !is_null($seminarSidang);
            return $skripsi;
        });

        // Stats (also filtered by gender)
        $user = $request->user();
        $baseQuery = Skripsi::where('is_active', true)
            ->whereIn('status', ['pengajuan_sidang_acc', 'sidang', 'revisi', 'lulus']);
        if ($user->role !== 'super_admin' && $user->jenis_kelamin) {
            $genderFilter = $user->jenis_kelamin;
            $baseQuery->whereHas('mahasiswa', function ($mq) use ($genderFilter) {
                $mq->where('jenis_kelamin', $genderFilter);
            });
        }
        $terjadwal = (clone $baseQuery)->whereHas('seminar', function ($q) {
            $q->where('jenis', 'sidang');
        })->count();
        $belum = (clone $baseQuery)->whereDoesntHave('seminar', function ($q) {
            $q->where('jenis', 'sidang');
        })->count();

        return response()->json([
            'success' => true,
            'data' => $skripsiList,
            'stats' => [
                'total' => $skripsiList->total(),
                'terjadwal' => $terjadwal,
                'belum' => $belum,
            ]
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
        if ($skripsi && $skripsi->status === 'pengajuan_sidang_acc') {
            $skripsi->update(['status' => 'sidang', 'progress_percentage' => 85]);
        }

        return response()->json([
            'success' => true,
            'message' => 'Ujian berhasil dijadwalkan',
            'data' => $ujian
        ], 201);
    }

    /**
     * Check if the current user is a female staff member
     */
    private function isFemaleStaff(Request $request): bool
    {
        $user = $request->user();
        if (!$user || $user->role !== 'staff') {
            return false;
        }
        $gender = strtolower(trim($user->jenis_kelamin ?? ''));
        return $gender === 'p' || $gender === 'perempuan';
    }

    /**
     * Create a pending SkripsiHistory for admin approval
     */
    private function createPendingStatusChange(Skripsi $skripsi, string $newStatus, int $userId, string $alasan = null): void
    {
        SkripsiHistory::create([
            'skripsi_id' => $skripsi->id,
            'judul_lama' => $skripsi->judul,
            'judul_baru' => $skripsi->judul,
            'status_lama' => $skripsi->status,
            'status_baru' => $newStatus,
            'alasan' => $alasan ?? 'Perubahan nilai ujian sidang oleh staff — menunggu persetujuan admin',
            'verification_status' => 'pending',
            'updated_by' => $userId,
        ]);
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

        // Determine if current user is female staff (needs admin approval for status changes)
        $needsApproval = $this->isFemaleStaff($request);
        $pendingApproval = false;

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
                $targetStatus = null;
                if ($validated['hasil'] === 'lulus') {
                    $targetStatus = 'lulus';
                } elseif ($validated['hasil'] === 'lulus_revisi') {
                    $targetStatus = 'revisi';
                }

                if ($targetStatus) {
                    if ($needsApproval) {
                        // Female staff: create pending approval
                        $this->createPendingStatusChange($skripsi, $targetStatus, $request->user()->id);
                        $pendingApproval = true;
                    } else {
                        $progressMap = ['lulus' => 100, 'revisi' => 90];
                        $skripsi->update([
                            'status' => $targetStatus,
                            'progress_percentage' => $progressMap[$targetStatus] ?? $skripsi->progress_percentage,
                        ]);
                    }
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
            $result = $this->recalculateNilai($ujian, $needsApproval, $request->user()->id);
            if ($result === 'pending') {
                $pendingApproval = true;
            }
        }

        $ujian->load(['penguji.dosen', 'skripsi.mahasiswa']);

        $message = 'Data ujian berhasil diperbarui';
        if ($pendingApproval) {
            $message .= '. Perubahan status mahasiswa menunggu persetujuan admin.';
        }

        return response()->json([
            'success' => true,
            'message' => $message,
            'data' => $this->formatUjianResponse($ujian),
            'pending_approval' => $pendingApproval,
        ]);
    }

    /**
     * Recalculate the average score from all penguji
     * Returns 'pending' if status change needs admin approval
     */
    private function recalculateNilai(Seminar $ujian, bool $skipStatusUpdate = false, int $userId = null): ?string
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
                    $targetStatus = null;
                    if ($finalHasil === 'lulus') {
                        $targetStatus = 'lulus';
                    } elseif ($finalHasil === 'lulus_revisi') {
                        $targetStatus = 'revisi';
                    }

                    if ($targetStatus) {
                        if ($skipStatusUpdate) {
                            // Female staff: defer to admin approval
                            $this->createPendingStatusChange($skripsi, $targetStatus, $userId);
                            $ujian->update($updateData);
                            return 'pending';
                        } else {
                            $progressMap = ['lulus' => 100, 'revisi' => 90];
                            $skripsi->update([
                                'status' => $targetStatus,
                                'progress_percentage' => $progressMap[$targetStatus] ?? $skripsi->progress_percentage,
                            ]);
                        }
                    }
                }
            }

            $ujian->update($updateData);
        }

        return null;
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

    /**
     * Delete an ujian (sidang) schedule.
     * Reverts skripsi status to pengajuan_sidang_acc so it can be re-scheduled.
     */
    public function destroy($id)
    {
        $ujian = Seminar::where('jenis', 'sidang')->findOrFail($id);

        // Revert skripsi status back to pengajuan_sidang_acc
        // Covers all statuses that could have resulted from sidang scheduling/scoring
        $skripsi = Skripsi::find($ujian->skripsi_id);
        if ($skripsi && in_array($skripsi->status, ['sidang', 'lulus', 'revisi'])) {
            $skripsi->update([
                'status' => 'pengajuan_sidang_acc',
                'progress_percentage' => 65,
            ]);
        }

        // Remove any pending verification records related to this skripsi's sidang
        if ($skripsi) {
            SkripsiHistory::where('skripsi_id', $skripsi->id)
                ->where('verification_status', 'pending')
                ->where('alasan', 'like', '%nilai ujian sidang%')
                ->delete();
        }

        // Delete penguji records
        Penguji::where('seminar_id', $ujian->id)->delete();

        // Delete the ujian
        $ujian->delete();

        return response()->json([
            'success' => true,
            'message' => 'Jadwal sidang berhasil dihapus dan status mahasiswa dikembalikan.',
        ]);
    }
}
