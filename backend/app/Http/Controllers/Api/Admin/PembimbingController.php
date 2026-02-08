<?php

namespace App\Http\Controllers\Api\Admin;

use App\Http\Controllers\Controller;
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
        $query = Skripsi::with(['mahasiswa.prodi'])
            ->where('is_active', true)
            ->whereIn('status', ['pengajuan', 'disetujui']);

        // Filter by those without pembimbing
        if ($request->get('pending_only', true)) {
            $query->whereDoesntHave('pembimbing');
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

        $dosen = $query->get()->filter(function ($d) {
            return $d->current_bimbingan < $d->kuota_bimbingan;
        });

        return response()->json([
            'success' => true,
            'data' => $dosen
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

        $skripsi = Skripsi::findOrFail($request->skripsi_id);

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

        // Update skripsi status
        if ($skripsi->status === 'pengajuan') {
            $skripsi->status = 'disetujui';
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

        $pembimbing->dosen_id = $request->dosen_id;
        $pembimbing->tanggal_penetapan = now();
        $pembimbing->save();

        return response()->json([
            'success' => true,
            'message' => 'Pembimbing berhasil diperbarui',
            'data' => $pembimbing->load('dosen')
        ]);
    }

    /**
     * Remove pembimbing
     */
    public function destroy(Pembimbing $pembimbing)
    {
        $pembimbing->is_active = false;
        $pembimbing->save();

        return response()->json([
            'success' => true,
            'message' => 'Pembimbing berhasil dihapus'
        ]);
    }
}
