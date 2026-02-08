<?php

namespace App\Http\Controllers\Api\Mahasiswa;

use App\Http\Controllers\Controller;
use App\Models\Skripsi;
use App\Models\Bimbingan;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class SkripsiController extends Controller
{
    /**
     * Get mahasiswa's skripsi (active and history)
     */
    public function index(Request $request)
    {
        $mahasiswa = $request->user()->mahasiswa;

        $skripsiList = Skripsi::with(['pembimbing.dosen'])
            ->where('mahasiswa_id', $mahasiswa->id)
            ->orderBy('created_at', 'desc')
            ->get();

        return response()->json([
            'success' => true,
            'data' => $skripsiList
        ]);
    }

    /**
     * Create new skripsi proposal
     */
    public function store(Request $request)
    {
        $request->validate([
            'judul' => 'required|string|max:500',
            'abstrak' => 'nullable|string',
            'kata_kunci' => 'nullable|string',
        ]);

        $mahasiswa = $request->user()->mahasiswa;

        // Check if already has active skripsi
        $activeSkripsi = $mahasiswa->activeSkripsi;
        if ($activeSkripsi) {
            return response()->json([
                'success' => false,
                'message' => 'Anda sudah memiliki skripsi aktif'
            ], 422);
        }

        $skripsi = Skripsi::create([
            'mahasiswa_id' => $mahasiswa->id,
            'judul' => $request->judul,
            'abstrak' => $request->abstrak,
            'kata_kunci' => $request->kata_kunci,
            'status' => 'pengajuan',
            'tanggal_daftar' => now(),
            'is_active' => true,
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Pengajuan skripsi berhasil dibuat',
            'data' => $skripsi
        ], 201);
    }

    /**
     * Get skripsi detail
     */
    public function show(Request $request)
    {
        $mahasiswa = $request->user()->mahasiswa;
        $skripsi = $mahasiswa->activeSkripsi;

        if (!$skripsi) {
            return response()->json([
                'success' => false,
                'message' => 'Skripsi tidak ditemukan'
            ], 404);
        }

        $skripsi->load([
            'pembimbing.dosen',
            'bimbingan.dosen',
            'seminar.penguji.dosen',
            'ujian.penguji.dosen',
            'dokumen',
            'nilai',
            'skTugas',
            'notaBimbingan',
            'history'
        ]);

        return response()->json([
            'success' => true,
            'data' => $skripsi
        ]);
    }

    /**
     * Update skripsi (only allowed in certain statuses)
     */
    public function update(Request $request)
    {
        $mahasiswa = $request->user()->mahasiswa;
        $skripsi = $mahasiswa->activeSkripsi;

        if (!$skripsi) {
            return response()->json([
                'success' => false,
                'message' => 'Skripsi tidak ditemukan'
            ], 404);
        }

        // Only allow updates in draft or revision status
        if (!in_array($skripsi->status, ['draft', 'ditolak'])) {
            return response()->json([
                'success' => false,
                'message' => 'Skripsi tidak dapat diubah pada status ini'
            ], 422);
        }

        $request->validate([
            'judul' => 'sometimes|string|max:500',
            'abstrak' => 'nullable|string',
            'kata_kunci' => 'nullable|string',
        ]);

        $skripsi->fill($request->only(['judul', 'abstrak', 'kata_kunci']));
        if ($skripsi->status === 'ditolak') {
            $skripsi->status = 'pengajuan';
        }
        $skripsi->save();

        return response()->json([
            'success' => true,
            'message' => 'Skripsi berhasil diperbarui',
            'data' => $skripsi
        ]);
    }

    /**
     * Get bimbingan logs
     */
    public function bimbingan(Request $request)
    {
        $mahasiswa = $request->user()->mahasiswa;
        $skripsi = $mahasiswa->activeSkripsi;

        if (!$skripsi) {
            return response()->json([
                'success' => false,
                'message' => 'Skripsi tidak ditemukan'
            ], 404);
        }

        $logs = Bimbingan::with('dosen')
            ->where('skripsi_id', $skripsi->id)
            ->orderBy('tanggal', 'desc')
            ->get();

        return response()->json([
            'success' => true,
            'data' => $logs
        ]);
    }

    /**
     * Add new bimbingan log
     */
    public function addBimbingan(Request $request)
    {
        $request->validate([
            'tanggal' => 'required|date',
            'topik' => 'required|string|max:255',
            'deskripsi' => 'nullable|string',
            'dosen_id' => 'required|exists:dosen,id',
            'file_bukti' => 'nullable|file|max:5120',
        ]);

        $mahasiswa = $request->user()->mahasiswa;
        $skripsi = $mahasiswa->activeSkripsi;

        if (!$skripsi) {
            return response()->json([
                'success' => false,
                'message' => 'Skripsi tidak ditemukan'
            ], 404);
        }

        $path = null;
        if ($request->hasFile('file_bukti')) {
            $path = $request->file('file_bukti')->store('bimbingan', 'public');
        }

        $bimbingan = Bimbingan::create([
            'skripsi_id' => $skripsi->id,
            'dosen_id' => $request->dosen_id,
            'tanggal' => $request->tanggal,
            'topik' => $request->topik,
            'deskripsi' => $request->deskripsi,
            'file_bukti' => $path,
            'status' => 'pending',
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Log bimbingan berhasil ditambahkan',
            'data' => $bimbingan->load('dosen')
        ], 201);
    }
}
