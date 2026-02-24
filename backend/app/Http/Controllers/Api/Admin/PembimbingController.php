<?php

namespace App\Http\Controllers\Api\Admin;

use App\Http\Controllers\Controller;
use App\Models\Configuration;
use App\Models\Pembimbing;
use App\Models\Skripsi;
use App\Models\Dosen;
use Illuminate\Http\Request;

class PembimbingController extends Controller
{
    /**
     * Display skripsi waiting for pembimbing assignment
     */
    public function index(Request $request)
    {
        $query = Skripsi::with(['mahasiswa.prodi', 'pembimbing.dosen'])
            ->where('is_active', true)
            ->whereIn('status', ['bimbingan', 'penentuan_dospem', 'dospem']);

        // Filter by pembimbing status: sudah / belum
        if ($request->filled('pembimbing_status')) {
            if ($request->pembimbing_status === 'sudah') {
                $query->whereHas('pembimbing');
            } elseif ($request->pembimbing_status === 'belum') {
                $query->whereDoesntHave('pembimbing');
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
     * Get available dosen for pembimbing assignment
     */
    public function availableDosen(Request $request)
    {
        // Get global kuota default
        $globalConfig = Configuration::where('key', 'kuota_bimbingan_default')->first();
        $globalKuota = $globalConfig ? ($globalConfig->value['kuota'] ?? 10) : 10;

        $query = Dosen::with('prodi')
            ->where('is_active', true)
            ->withCount(['pembimbing as current_bimbingan' => function ($q) {
                $q->where('is_active', true);
            }]);

        if ($request->filled('prodi_id')) {
            $query->where('prodi_id', $request->prodi_id);
        }

        if ($request->filled('bidang_keahlian')) {
            $query->where('bidang_keahlian', 'like', "%{$request->bidang_keahlian}%");
        }

        $dosen = $query->orderBy('nama', 'asc')->get()->map(function ($d) use ($globalKuota) {
            $kuota = $d->kuota_bimbingan ?: $globalKuota;
            return [
                'id' => $d->id,
                'nama' => $d->nama,
                'nama_lengkap' => $d->full_name,
                'bidang_keahlian' => $d->bidang_keahlian,
                'kuota_bimbingan' => $kuota,
                'current_bimbingan' => $d->current_bimbingan,
                'is_available' => $d->current_bimbingan < $kuota,
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
     * Assign pembimbing to skripsi
     */
    public function store(Request $request)
    {
        $request->validate([
            'skripsi_id' => 'required|exists:skripsi,id',
            'pembimbing_1_id' => 'required|exists:dosen,id',
            'pembimbing_2_id' => 'nullable|exists:dosen,id|different:pembimbing_1_id',
        ]);

        $skripsi = Skripsi::with('mahasiswa')->findOrFail($request->skripsi_id);

        // Check kuota for pembimbing 1
        $globalConfig = Configuration::where('key', 'kuota_bimbingan_default')->first();
        $globalKuota = $globalConfig ? ($globalConfig->value['kuota'] ?? 10) : 10;

        $dosen1 = Dosen::withCount(['pembimbing as current_bimbingan' => function ($q) {
            $q->where('is_active', true);
        }])->findOrFail($request->pembimbing_1_id);
        $kuota1 = $dosen1->kuota_bimbingan ?: $globalKuota;

        // Only check quota if this is a NEW assignment (not re-assigning the same dosen)
        $existingPemb1 = Pembimbing::where('skripsi_id', $skripsi->id)
            ->where('jenis', 'pembimbing_1')
            ->where('dosen_id', $request->pembimbing_1_id)
            ->first();
        if (!$existingPemb1 && $dosen1->current_bimbingan >= $kuota1) {
            return response()->json([
                'success' => false,
                'message' => 'Kuota bimbingan dosen ' . $dosen1->nama . ' sudah penuh (' . $dosen1->current_bimbingan . '/' . $kuota1 . ')',
            ], 422);
        }

        if ($request->pembimbing_2_id) {
            $dosen2 = Dosen::withCount(['pembimbing as current_bimbingan' => function ($q) {
                $q->where('is_active', true);
            }])->findOrFail($request->pembimbing_2_id);
            $kuota2 = $dosen2->kuota_bimbingan ?: $globalKuota;

            $existingPemb2 = Pembimbing::where('skripsi_id', $skripsi->id)
                ->where('jenis', 'pembimbing_2')
                ->where('dosen_id', $request->pembimbing_2_id)
                ->first();
            if (!$existingPemb2 && $dosen2->current_bimbingan >= $kuota2) {
                return response()->json([
                    'success' => false,
                    'message' => 'Kuota bimbingan dosen ' . $dosen2->nama . ' sudah penuh (' . $dosen2->current_bimbingan . '/' . $kuota2 . ')',
                ], 422);
            }
        }

        // Track old dosen for side effects
        $oldPembimbing1 = Pembimbing::where('skripsi_id', $skripsi->id)->where('jenis', 'pembimbing_1')->first();
        $oldPembimbing2 = Pembimbing::where('skripsi_id', $skripsi->id)->where('jenis', 'pembimbing_2')->first();

        // Create pembimbing 1
        Pembimbing::updateOrCreate(
            ['skripsi_id' => $skripsi->id, 'jenis' => 'pembimbing_1'],
            [
                'dosen_id' => $request->pembimbing_1_id,
                'tanggal_penetapan' => now(),
                'is_active' => true,
            ]
        );

        // Create pembimbing 2 if provided
        if ($request->pembimbing_2_id) {
            Pembimbing::updateOrCreate(
                ['skripsi_id' => $skripsi->id, 'jenis' => 'pembimbing_2'],
                [
                    'dosen_id' => $request->pembimbing_2_id,
                    'tanggal_penetapan' => now(),
                    'is_active' => true,
                ]
            );
        }

        // Check if any dosen actually changed → apply side effects
        $dosenChanged = false;
        if ($oldPembimbing1 && (int)$oldPembimbing1->dosen_id !== (int)$request->pembimbing_1_id) {
            $this->handleDosenChange($skripsi, $oldPembimbing1->dosen_id, $oldPembimbing1->id);
            $dosenChanged = true;
        }
        if ($oldPembimbing2 && $request->pembimbing_2_id && (int)$oldPembimbing2->dosen_id !== (int)$request->pembimbing_2_id) {
            $this->handleDosenChange($skripsi, $oldPembimbing2->dosen_id, $oldPembimbing2->id);
            $dosenChanged = true;
        }

        // Reset SK Tugas dokumen if any dosen changed
        if ($dosenChanged) {
            \App\Models\Dokumen::where('skripsi_id', $skripsi->id)
                ->where('jenis', 'sk_tugas')
                ->delete();
            // Reset status to dospem since SK Tugas needs regeneration
            if ($skripsi->status === 'bimbingan') {
                $skripsi->status = 'dospem';
                $skripsi->save();
            }
        }

        // Update skripsi status to dospem when pembimbing assigned
        if (in_array($skripsi->status, ['pengajuan', 'penentuan_dospem'])) {
            $skripsi->status = 'dospem';
            $skripsi->save();
        }

        return response()->json([
            'success' => true,
            'message' => 'Pembimbing berhasil ditetapkan',
            'data' => $skripsi->load(['pembimbing.dosen'])
        ]);
    }

    /**
     * Update pembimbing assignment
     */
    public function update(Request $request, Pembimbing $pembimbing)
    {
        $request->validate([
            'dosen_id' => 'required|exists:dosen,id',
        ]);

        $oldDosenId = $pembimbing->dosen_id;
        $newDosenId = $request->dosen_id;

        // Only process side effects if the dosen actually changed
        if ((int)$oldDosenId !== (int)$newDosenId) {
            $skripsi = Skripsi::with('mahasiswa')->find($pembimbing->skripsi_id);

            // Reset SK Tugas dokumen status
            \App\Models\Dokumen::where('skripsi_id', $pembimbing->skripsi_id)
                ->where('jenis', 'sk_tugas')
                ->delete();

            // Remove old dosen conversations
            $this->handleDosenChange($skripsi, $oldDosenId, $pembimbing->id);
        }

        $pembimbing->dosen_id = $newDosenId;
        $pembimbing->tanggal_penetapan = now();
        $pembimbing->save();

        return response()->json([
            'success' => true,
            'message' => 'Pembimbing berhasil diperbarui',
            'data' => $pembimbing->load('dosen')
        ]);
    }

    /**
     * Handle side effects when a dosen is removed from pembimbing.
     * Removes chat conversations between mahasiswa and old dosen.
     */
    private function handleDosenChange($skripsi, $oldDosenId, $excludePembimbingId = null)
    {
        if (!$skripsi || !$skripsi->mahasiswa) return;

        $mahasiswaUserId = $skripsi->mahasiswa->user_id;
        $oldDosen = \App\Models\Dosen::find($oldDosenId);

        if (!$mahasiswaUserId || !$oldDosen || !$oldDosen->user_id) return;

        // Check if old dosen is still assigned as another pembimbing for this mahasiswa
        $query = Pembimbing::where('skripsi_id', $skripsi->id)
            ->where('dosen_id', $oldDosenId)
            ->where('is_active', true);

        if ($excludePembimbingId) {
            $query->where('id', '!=', $excludePembimbingId);
        }

        if ($query->exists()) return; // Still assigned elsewhere, don't remove chat

        // Delete conversations between mahasiswa and old dosen
        $oldDosenUserId = $oldDosen->user_id;
        $conversations = \App\Models\Conversation::forUser($mahasiswaUserId)
            ->whereHas('participants', function ($q) use ($oldDosenUserId) {
                $q->where('users.id', $oldDosenUserId);
            })->get();

        foreach ($conversations as $conv) {
            $conv->messages()->delete();
            $conv->participants()->detach();
            $conv->delete();
        }
    }

    /**
     * Remove pembimbing
     */
    public function destroy(Pembimbing $pembimbing)
    {
        $skripsi = $pembimbing->skripsi;
        $skripsi->status = 'penentuan_dospem';
        $skripsi->save();
        $pembimbing->delete();

        return response()->json([
            'success' => true,
            'message' => 'Pembimbing berhasil dihapus'
        ]);
    }
}
