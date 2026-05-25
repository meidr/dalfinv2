<?php

namespace App\Http\Controllers\Api\Dosen;

use App\Http\Controllers\Controller;
use App\Models\Seminar;
use App\Models\Penguji;
use App\Models\BeritaAcara;
use App\Models\PerbaikanProposal;
use App\Services\NomorSuratService;
use Illuminate\Http\Request;

class SeminarNilaiController extends Controller
{
    /**
     * Get seminar detail with all penguji scores
     */
    public function show(Request $request, $seminarId)
    {
        $dosen = $request->user()->dosen;

        $seminar = Seminar::with([
            'skripsi.mahasiswa.prodi',
            'skripsi.pembimbing.dosen',
            'penguji.dosen',
            'perbaikanProposal',
        ])->findOrFail($seminarId);

        // Verify dosen is penguji or pembimbing
        $ownPenguji = $seminar->penguji->firstWhere('dosen_id', $dosen->id);
        $isPembimbing = $seminar->skripsi->pembimbing->contains('dosen_id', $dosen->id);

        if (!$ownPenguji && !$isPembimbing) {
            return response()->json([
                'success' => false,
                'message' => 'Anda tidak memiliki akses ke seminar ini'
            ], 403);
        }

        $data = $seminar->toArray();
        $data['is_penguji'] = !!$ownPenguji;
        $data['own_penguji'] = $ownPenguji;
        $data['grade'] = $seminar->nilai ? $this->getGrade($seminar->nilai) : null;

        // Check if all penguji have scored
        $totalPenguji = $seminar->penguji->count();
        $scoredPenguji = $seminar->penguji->filter(fn($p) => $p->nilai !== null)->count();
        $data['all_scored'] = $totalPenguji > 0 && $scoredPenguji === $totalPenguji;
        $data['scored_count'] = $scoredPenguji;
        $data['total_penguji'] = $totalPenguji;

        return response()->json([
            'success' => true,
            'data' => $data,
        ]);
    }

    /**
     * Submit or update nilai for the logged-in dosen penguji
     */
    public function submitNilai(Request $request, $seminarId)
    {
        $dosen = $request->user()->dosen;

        $seminar = Seminar::with('penguji')->findOrFail($seminarId);

        // Check that this dosen is a penguji for this seminar
        $penguji = $seminar->penguji->firstWhere('dosen_id', $dosen->id);
        if (!$penguji) {
            return response()->json([
                'success' => false,
                'message' => 'Anda bukan penguji untuk seminar ini'
            ], 403);
        }

        $rules = [
            'nilai_mt' => 'required|numeric|min:0|max:100',
            'nilai_ms' => 'required|numeric|min:0|max:100',
            'nilai_pm' => 'required|numeric|min:0|max:100',
            'nilai_pi' => 'required|numeric|min:0|max:100',
            'catatan' => 'nullable|string',
        ];

        // Only ketua penguji can set hasil
        if ($penguji->peran === 'ketua') {
            $rules['hasil'] = 'nullable|in:lulus,lulus_revisi,tidak_lulus';
            // Perbaikan proposal items (only for sempro)
            if ($seminar->jenis === 'sempro') {
                $rules['perbaikan'] = 'nullable|array';
                $rules['perbaikan.*.topik'] = 'required|string|max:255';
                $rules['perbaikan.*.halaman'] = 'nullable|string|max:50';
                $rules['perbaikan.*.uraian'] = 'nullable|string';
            }
        }

        $validated = $request->validate($rules);

        // Calculate per-penguji average
        $avg = round(
            ($validated['nilai_mt'] + $validated['nilai_ms'] + $validated['nilai_pm'] + $validated['nilai_pi']) / 4,
            2
        );

        // Update this penguji's scores
        $penguji->update([
            'nilai_mt' => $validated['nilai_mt'],
            'nilai_ms' => $validated['nilai_ms'],
            'nilai_pm' => $validated['nilai_pm'],
            'nilai_pi' => $validated['nilai_pi'],
            'nilai' => $avg,
            'catatan' => $validated['catatan'] ?? null,
        ]);

        // If ketua and has set hasil, save it to seminar
        $ketuaHasil = null;
        if ($penguji->peran === 'ketua' && isset($validated['hasil'])) {
            $ketuaHasil = $validated['hasil'];
        }

        // Save perbaikan proposal items (ketua + sempro only)
        if ($penguji->peran === 'ketua' && $seminar->jenis === 'sempro' && isset($validated['perbaikan'])) {
            // Delete existing and re-create
            $seminar->perbaikanProposal()->delete();
            foreach ($validated['perbaikan'] as $index => $item) {
                $seminar->perbaikanProposal()->create([
                    'no' => $index + 1,
                    'topik' => $item['topik'],
                    'halaman' => $item['halaman'] ?? null,
                    'uraian' => $item['uraian'] ?? null,
                ]);
            }
        }

        // Recalculate seminar average if all penguji scored
        $this->recalculateNilai($seminar, $ketuaHasil);

        // Reload
        $seminar->load('penguji.dosen');

        return response()->json([
            'success' => true,
            'message' => 'Nilai berhasil disimpan',
            'data' => [
                'penguji' => $penguji->fresh(),
                'seminar_nilai' => $seminar->fresh()->nilai,
                'seminar_grade' => $seminar->fresh()->nilai ? $this->getGrade($seminar->fresh()->nilai) : null,
                'seminar_hasil' => $seminar->fresh()->hasil,
            ],
        ]);
    }

    /**
     * Recalculate seminar average from all penguji scores
     */
    private function recalculateNilai(Seminar $seminar, ?string $ketuaHasil = null)
    {
        $pengujiScores = Penguji::where('seminar_id', $seminar->id)
            ->whereNotNull('nilai')
            ->pluck('nilai');

        if ($pengujiScores->count() > 0) {
            $average = round($pengujiScores->avg(), 2);
            $updateData = ['nilai' => $average];

            $totalPenguji = Penguji::where('seminar_id', $seminar->id)->count();
            if ($pengujiScores->count() === $totalPenguji) {
                // Use ketua's hasil if provided, otherwise use existing or auto-determine
                if ($ketuaHasil) {
                    $updateData['hasil'] = $ketuaHasil;
                } elseif (!$seminar->hasil) {
                    // Auto-determine only if no hasil set yet
                    $updateData['hasil'] = $average >= 55 ? 'lulus' : 'tidak_lulus';
                }
                $updateData['status'] = 'selesai';

                // Auto-create or update BeritaAcara
                if (!$seminar->beritaAcara) {
                    $baNomor = app(NomorSuratService::class)->generateForSeminar(
                        NomorSuratService::keyForBeritaAcara($seminar->jenis),
                        $seminar
                    );
                    BeritaAcara::create([
                        'jenis' => 'seminar',
                        'seminar_id' => $seminar->id,
                        'nomor' => $baNomor,
                        'tanggal' => now(),
                        'hasil' => $updateData['hasil'] ?? ($ketuaHasil ?? 'lulus'),
                        'catatan' => null,
                    ]);
                } else {
                    app(NomorSuratService::class)->ensureBeritaAcaraNumber(
                        $seminar->beritaAcara,
                        $seminar,
                        $seminar->beritaAcara->tanggal
                    );
                }

                // Update skripsi status to penentuan_dospem only if sempro and lulus/lulus_revisi
                $skripsi = $seminar->skripsi;
                $finalHasil = $updateData['hasil'] ?? $seminar->hasil;
                if (
                    $skripsi && in_array($skripsi->status, ['proposal', 'sempro'])
                    && $seminar->jenis === 'sempro'
                    && in_array($finalHasil, ['lulus', 'lulus_revisi'])
                ) {
                    $skripsi->update(['status' => 'penentuan_dospem']);
                }
            }

            // If ketua explicitly sets hasil even before all scored
            if ($ketuaHasil) {
                $updateData['hasil'] = $ketuaHasil;
            }

            $seminar->update($updateData);
        }
    }

    private function getGrade($nilai): string
    {
        if ($nilai >= 85) return 'A';
        if ($nilai >= 80) return 'B+';
        if ($nilai >= 70) return 'B';
        if ($nilai >= 65) return 'C+';
        if ($nilai >= 55) return 'C';
        return 'D';
    }
}
