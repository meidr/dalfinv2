<?php

namespace App\Http\Controllers\Api\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Seminar;

class UjianController extends Controller
{
    public function index(Request $request)
    {
        $query = Seminar::with([
            'skripsi.mahasiswa',
            'skripsi.pembimbing.dosen'
        ])->where('jenis', 'ujian');

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

        $ujian = $query->orderBy('tanggal', 'desc')->paginate(10);

        return response()->json([
            'success' => true,
            'data' => $ujian
        ]);
    }

    public function show($id)
    {
        $ujian = Seminar::with([
            'skripsi.mahasiswa',
            'skripsi.pembimbing.dosen',
            'beritaAcara'
        ])->where('jenis', 'ujian')->findOrFail($id);

        return response()->json([
            'success' => true,
            'data' => $ujian
        ]);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'skripsi_id' => 'required|exists:skripsi,id',
            'tanggal' => 'required|date',
            'waktu' => 'required|string',
            'ruangan' => 'required|string',
        ]);

        $ujian = Seminar::create([
            'skripsi_id' => $validated['skripsi_id'],
            'jenis' => 'ujian',
            'tanggal' => $validated['tanggal'],
            'waktu' => $validated['waktu'],
            'ruangan' => $validated['ruangan'],
            'status' => 'pending',
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Ujian berhasil dijadwalkan',
            'data' => $ujian
        ], 201);
    }

    public function update(Request $request, $id)
    {
        $ujian = Seminar::where('jenis', 'ujian')->findOrFail($id);

        $validated = $request->validate([
            'tanggal' => 'sometimes|date',
            'waktu' => 'sometimes|string',
            'ruangan' => 'sometimes|string',
            'status' => 'sometimes|string|in:pending,selesai,batal',
            'nilai' => 'sometimes|numeric|min:0|max:100',
            'hasil' => 'sometimes|string|in:lulus,lulus_revisi,tidak_lulus',
            'catatan' => 'nullable|string',
        ]);

        $ujian->update($validated);

        return response()->json([
            'success' => true,
            'message' => 'Data ujian berhasil diperbarui',
            'data' => $ujian
        ]);
    }
}
