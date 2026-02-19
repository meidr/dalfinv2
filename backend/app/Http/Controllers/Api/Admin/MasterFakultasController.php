<?php

namespace App\Http\Controllers\Api\Admin;

use App\Http\Controllers\Controller;
use App\Models\Fakultas;
use Illuminate\Http\Request;

class MasterFakultasController extends Controller
{
    public function index(Request $request)
    {
        $query = Fakultas::with(['dekan', 'wakilDekan']);

        if ($request->has('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('kode', 'like', "%{$search}%")
                    ->orWhere('nama_fakultas', 'like', "%{$search}%");
            });
        }

        if ($request->has('active_only')) {
            $query->where('is_active', true);
        }

        $fakultas = $query->orderBy('nama_fakultas', 'asc')->get();

        return response()->json([
            'success' => true,
            'data' => $fakultas
        ]);
    }

    public function store(Request $request)
    {
        $request->validate([
            'kode' => 'required|string|max:20|unique:fakultas,kode',
            'nama_fakultas' => 'required|string|max:255',
            'dekan_id' => 'nullable|exists:dosen,id',
            'wakil_dekan_id' => 'nullable|exists:dosen,id',
            'is_active' => 'boolean',
        ]);

        $fakultas = Fakultas::create($request->all());
        $fakultas->load(['dekan', 'wakilDekan']);

        return response()->json([
            'success' => true,
            'message' => 'Fakultas berhasil ditambahkan',
            'data' => $fakultas
        ]);
    }

    public function update(Request $request, $id)
    {
        $request->validate([
            'kode' => 'required|string|max:20|unique:fakultas,kode,' . $id,
            'nama_fakultas' => 'required|string|max:255',
            'dekan_id' => 'nullable|exists:dosen,id',
            'wakil_dekan_id' => 'nullable|exists:dosen,id',
            'is_active' => 'boolean',
        ]);

        $fakultas = Fakultas::findOrFail($id);
        $fakultas->update($request->all());
        $fakultas->load(['dekan', 'wakilDekan']);

        return response()->json([
            'success' => true,
            'message' => 'Fakultas berhasil diperbarui',
            'data' => $fakultas
        ]);
    }

    public function destroy($id)
    {
        $fakultas = Fakultas::findOrFail($id);

        if ($fakultas->prodi()->exists()) {
            return response()->json([
                'success' => false,
                'message' => 'Tidak dapat menghapus Fakultas yang memiliki Program Studi'
            ], 422);
        }

        $fakultas->delete();

        return response()->json([
            'success' => true,
            'message' => 'Fakultas berhasil dihapus'
        ]);
    }
}
