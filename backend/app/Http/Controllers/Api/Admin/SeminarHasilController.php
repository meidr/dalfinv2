<?php

namespace App\Http\Controllers\Api\Admin;

use App\Http\Controllers\Controller;
use App\Models\Skripsi;
use App\Models\Seminar;
use App\Models\Penguji;
use App\Models\BeritaAcara;
use Illuminate\Http\Request;

class SeminarHasilController extends Controller
{
    /**
     * List skripsi eligible for seminar hasil
     * Shows skripsi with status: bimbingan, semhas, ujian
     */
    public function index(Request $request)
    {
        $query = Skripsi::with(['mahasiswa.prodi', 'pembimbing.dosen', 'seminar' => function ($q) {
            $q->where('jenis', 'semhas')->latest('tanggal');
        }])
            ->whereIn('status', ['bimbingan', 'semhas', 'sidang']);

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

        // Status filter
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        // Jadwal filter: terjadwal or belum
        if ($request->filled('jadwal')) {
            if ($request->jadwal === 'terjadwal') {
                $query->whereHas('seminar', function ($q) {
                    $q->where('jenis', 'semhas');
                });
            } elseif ($request->jadwal === 'belum') {
                $query->whereDoesntHave('seminar', function ($q) {
                    $q->where('jenis', 'semhas');
                });
            }
        }

        $perPage = $request->get('per_page', 15);
        $skripsiList = $query->orderBy('created_at', 'desc')->paginate($perPage);

        // Add computed fields
        $skripsiList->getCollection()->transform(function ($skripsi) {
            $seminarSemhas = $skripsi->seminar->where('jenis', 'semhas')->first();
            $skripsi->semhas_seminar = $seminarSemhas;
            $skripsi->is_scheduled = !is_null($seminarSemhas);
            return $skripsi;
        });

        return response()->json([
            'success' => true,
            'data' => $skripsiList
        ]);
    }

    /**
     * Schedule a new seminar hasil
     */
    public function store(Request $request)
    {
        $request->validate([
            'skripsi_id' => 'required|exists:skripsi,id',
            'tanggal' => 'required|date',
            'waktu' => 'required',
            'ruangan' => 'required|string|max:100',
            'penguji' => 'array|min:2|max:5',
            'penguji.*.dosen_id' => 'required|exists:dosen,id',
            'penguji.*.peran' => 'required|in:ketua,penguji_1,penguji_2',
        ]);

        $seminar = Seminar::create([
            'skripsi_id' => $request->skripsi_id,
            'jenis' => 'semhas',
            'tanggal' => $request->tanggal,
            'waktu' => $request->waktu,
            'ruangan' => $request->ruangan,
            'status' => 'terjadwal',
        ]);

        // Add penguji
        if ($request->has('penguji')) {
            foreach ($request->penguji as $p) {
                Penguji::create([
                    'seminar_id' => $seminar->id,
                    'dosen_id' => $p['dosen_id'],
                    'peran' => $p['peran'],
                ]);
            }
        }

        return response()->json([
            'success' => true,
            'message' => 'Seminar hasil berhasil dijadwalkan',
            'data' => $seminar->load(['skripsi.mahasiswa', 'penguji.dosen'])
        ], 201);
    }

    /**
     * Show seminar detail
     */
    public function show(Seminar $seminar)
    {
        $seminar->load([
            'skripsi.mahasiswa.prodi',
            'skripsi.pembimbing.dosen',
            'penguji.dosen',
            'beritaAcara'
        ]);

        return response()->json([
            'success' => true,
            'data' => $seminar
        ]);
    }

    /**
     * Update seminar
     */
    public function update(Request $request, Seminar $seminar)
    {
        $request->validate([
            'tanggal' => 'sometimes|date',
            'waktu' => 'sometimes',
            'ruangan' => 'sometimes|string|max:100',
            'status' => 'sometimes|in:terjadwal,berlangsung,selesai,batal',
            'nilai' => 'nullable|numeric|min:0|max:100',
            'catatan' => 'nullable|string',
        ]);

        $seminar->fill($request->only([
            'tanggal',
            'waktu',
            'ruangan',
            'status',
            'nilai',
            'catatan'
        ]));
        $seminar->save();

        return response()->json([
            'success' => true,
            'message' => 'Seminar hasil berhasil diperbarui',
            'data' => $seminar->load(['penguji.dosen'])
        ]);
    }

    /**
     * Delete seminar
     */
    public function destroy(Seminar $seminar)
    {
        $seminar->penguji()->delete();
        $seminar->beritaAcara()->delete();
        $seminar->delete();

        return response()->json([
            'success' => true,
            'message' => 'Seminar hasil berhasil dihapus'
        ]);
    }

    /**
     * Create berita acara
     */
    public function createBeritaAcara(Request $request, Seminar $seminar)
    {
        $request->validate([
            'nomor' => 'required|string|unique:berita_acara,nomor',
            'hasil' => 'required|in:lulus,lulus_bersyarat,tidak_lulus,mengulang',
            'catatan' => 'nullable|string',
        ]);

        $beritaAcara = BeritaAcara::create([
            'jenis' => 'seminar',
            'seminar_id' => $seminar->id,
            'nomor' => $request->nomor,
            'tanggal' => now(),
            'hasil' => $request->hasil,
            'catatan' => $request->catatan,
        ]);

        $seminar->status = 'selesai';
        $seminar->save();

        return response()->json([
            'success' => true,
            'message' => 'Berita acara berhasil dibuat',
            'data' => $beritaAcara
        ], 201);
    }

    /**
     * Add a penguji to a seminar
     */
    public function addPenguji(Request $request, Seminar $seminar)
    {
        $request->validate([
            'dosen_id' => 'required|exists:dosen,id',
            'peran' => 'required|in:ketua,penguji_1,penguji_2',
        ]);

        $exists = Penguji::where('seminar_id', $seminar->id)
            ->where('dosen_id', $request->dosen_id)
            ->exists();

        if ($exists) {
            return response()->json([
                'success' => false,
                'message' => 'Dosen sudah ditugaskan sebagai penguji'
            ], 422);
        }

        $penguji = Penguji::create([
            'seminar_id' => $seminar->id,
            'dosen_id' => $request->dosen_id,
            'peran' => $request->peran,
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Penguji berhasil ditambahkan',
            'data' => $penguji->load('dosen')
        ], 201);
    }

    /**
     * Update a penguji (nilai, catatan, peran)
     */
    public function updatePenguji(Request $request, Seminar $seminar, Penguji $penguji)
    {
        $request->validate([
            'peran' => 'sometimes|in:ketua,penguji_1,penguji_2',
            'nilai' => 'nullable|numeric|min:0|max:100',
            'catatan' => 'nullable|string',
        ]);

        $penguji->fill($request->only(['peran', 'nilai', 'catatan']));
        $penguji->save();

        return response()->json([
            'success' => true,
            'message' => 'Penguji berhasil diperbarui',
            'data' => $penguji->load('dosen')
        ]);
    }

    /**
     * Remove a penguji from a seminar
     */
    public function removePenguji(Seminar $seminar, Penguji $penguji)
    {
        $penguji->delete();

        return response()->json([
            'success' => true,
            'message' => 'Penguji berhasil dihapus'
        ]);
    }
}
