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
        $query = Skripsi::with(['mahasiswa.prodi', 'pembimbing.dosen'])
            ->where('is_active', true)
            ->where('status', 'bimbingan');

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

        $dosen = $query->orderBy('nama', 'asc')->get()->map(function ($d) {
            return [
                'id' => $d->id,
                'nama' => $d->nama,
                'nama_lengkap' => $d->full_name,
                'bidang_keahlian' => $d->bidang_keahlian,
                'kuota_bimbingan' => $d->kuota_bimbingan,
                'current_bimbingan' => $d->current_bimbingan,
                'is_available' => $d->current_bimbingan < $d->kuota_bimbingan,
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
        $pembimbing->delete();

        return response()->json([
            'success' => true,
            'message' => 'Pembimbing berhasil dihapus'
        ]);
    }
}
