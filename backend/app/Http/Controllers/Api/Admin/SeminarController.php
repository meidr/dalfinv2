<?php

namespace App\Http\Controllers\Api\Admin;

use App\Http\Controllers\Controller;
use App\Models\Skripsi;
use App\Models\Seminar;
use App\Models\Penguji;
use App\Models\BeritaAcara;
use Illuminate\Http\Request;

class SeminarController extends Controller
{
    /**
     * List skripsi eligible for seminar proposal
     * Shows skripsi with status: pengajuan, proposal, sempro
     */
    public function index(Request $request)
    {
        $query = Skripsi::with(['mahasiswa.prodi', 'pembimbing.dosen', 'seminar' => function ($q) {
            $q->where('jenis', 'sempro')->latest('tanggal');
        }])
            ->where('is_active', true)
            ->whereIn('status', ['pengajuan', 'proposal', 'sempro', 'bimbingan']);

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
                    $q->where('jenis', 'sempro');
                });
            } elseif ($request->jadwal === 'belum') {
                $query->whereDoesntHave('seminar', function ($q) {
                    $q->where('jenis', 'sempro');
                });
            }
        }

        $perPage = $request->get('per_page', 15);
        $skripsiList = $query->orderBy('created_at', 'desc')->paginate($perPage);

        // Add computed fields
        $skripsiList->getCollection()->transform(function ($skripsi) {
            $seminarSempro = $skripsi->seminar->where('jenis', 'sempro')->first();
            $skripsi->sempro_seminar = $seminarSempro;
            $skripsi->is_scheduled = !is_null($seminarSempro);
            return $skripsi;
        });

        return response()->json([
            'success' => true,
            'data' => $skripsiList
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
            'penguji.*.peran' => 'required|in:ketua,penguji_1,penguji_2',
        ]);

        // Update skripsi status to 'proposal'
        $skripsi = Skripsi::findOrFail($request->skripsi_id);
        if (in_array($skripsi->status, ['pengajuan', 'draft', 'ditolak'])) {
            $skripsi->status = 'sempro';
            $skripsi->save();
        }

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
            'message' => 'Seminar berhasil diperbarui',
            'data' => $seminar->load(['penguji.dosen'])
        ]);
    }

    /**
     * Delete seminar
     */
    public function destroy(Seminar $seminar)
    {
        // Delete related records first
        $seminar->penguji()->delete();
        $seminar->beritaAcara()->delete();
        $seminar->delete();

        return response()->json([
            'success' => true,
            'message' => 'Seminar berhasil dihapus'
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

    /**
     * Add a penguji to a seminar
     */
    public function addPenguji(Request $request, Seminar $seminar)
    {
        $request->validate([
            'dosen_id' => 'required|exists:dosen,id',
            'peran' => 'required|in:ketua,penguji_1,penguji_2',
        ]);

        // Check if dosen already assigned
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

    /**
     * Upload or re-upload proposal document for the seminar's skripsi
     */
    public function uploadProposal(Request $request, Seminar $seminar)
    {
        $request->validate([
            'file_skripsi' => 'required|file|mimes:pdf,doc,docx|max:10240',
        ]);

        $skripsi = $seminar->skripsi;

        // Delete old file if exists
        if ($skripsi->file_skripsi && \Illuminate\Support\Facades\Storage::disk('public')->exists($skripsi->file_skripsi)) {
            \Illuminate\Support\Facades\Storage::disk('public')->delete($skripsi->file_skripsi);
        }

        $file = $request->file('file_skripsi');
        $fileName = time() . '_' . $file->getClientOriginalName();
        $skripsi->file_skripsi = $file->storeAs('skripsi_files', $fileName, 'public');
        $skripsi->save();

        return response()->json([
            'success' => true,
            'message' => 'Dokumen proposal berhasil diupload',
            'data' => [
                'file_skripsi_url' => $skripsi->file_skripsi_url,
            ]
        ]);
    }
}
