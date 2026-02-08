<?php

namespace App\Http\Controllers\Api\Admin;

use App\Http\Controllers\Controller;
use App\Models\Seminar;
use App\Models\Penguji;
use App\Models\BeritaAcara;
use Illuminate\Http\Request;

class SeminarController extends Controller
{
    /**
     * List all seminars
     */
    public function index(Request $request)
    {
        $query = Seminar::with(['skripsi.mahasiswa', 'penguji.dosen']);

        if ($request->filled('jenis')) {
            $query->where('jenis', $request->jenis);
        }

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        if ($request->filled('from_date')) {
            $query->where('tanggal', '>=', $request->from_date);
        }

        if ($request->filled('to_date')) {
            $query->where('tanggal', '<=', $request->to_date);
        }

        $perPage = $request->get('per_page', 15);
        $seminars = $query->orderBy('tanggal', 'desc')->paginate($perPage);

        return response()->json([
            'success' => true,
            'data' => $seminars
        ]);
    }

    /**
     * Schedule a new seminar
     */
    public function store(Request $request)
    {
        $request->validate([
            'skripsi_id' => 'required|exists:skripsi,id',
            'jenis' => 'required|in:sempro,semhas',
            'tanggal' => 'required|date',
            'waktu' => 'required',
            'ruangan' => 'required|string|max:100',
            'penguji' => 'array|min:2|max:5',
            'penguji.*.dosen_id' => 'required|exists:dosen,id',
            'penguji.*.peran' => 'required|in:ketua,sekretaris,anggota',
        ]);

        $seminar = Seminar::create([
            'skripsi_id' => $request->skripsi_id,
            'jenis' => $request->jenis,
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
            'message' => 'Seminar berhasil dijadwalkan',
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
            'tanggal', 'waktu', 'ruangan', 'status', 'nilai', 'catatan'
        ]));
        $seminar->save();

        return response()->json([
            'success' => true,
            'message' => 'Seminar berhasil diperbarui',
            'data' => $seminar->load(['penguji.dosen'])
        ]);
    }

    /**
     * Delete seminar
     */
    public function destroy(Seminar $seminar)
    {
        $seminar->status = 'batal';
        $seminar->save();

        return response()->json([
            'success' => true,
            'message' => 'Seminar berhasil dibatalkan'
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

        // Update seminar status
        $seminar->status = 'selesai';
        $seminar->save();

        return response()->json([
            'success' => true,
            'message' => 'Berita acara berhasil dibuat',
            'data' => $beritaAcara
        ], 201);
    }
}
