<?php

namespace App\Http\Controllers\Api\Admin;

use App\Http\Controllers\Controller;
use App\Models\MasterJabatan;
use Illuminate\Http\Request;

class MasterJabatanController extends Controller
{
    public function index(Request $request)
    {
        $query = MasterJabatan::query();

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('nama', 'like', "%{$search}%")
                    ->orWhere('kode', 'like', "%{$search}%");
            });
        }

        if ($request->filled('level')) {
            $query->where('level', $request->level);
        }

        $jabatans = $query->orderBy('level')->orderBy('nama')->get();

        return response()->json([
            'success' => true,
            'data' => $jabatans
        ]);
    }

    public function store(Request $request)
    {
        $request->validate([
            'kode' => 'required|string|max:30|unique:master_jabatan,kode',
            'nama' => 'required|string|max:100',
            'level' => 'required|in:kampus,fakultas,prodi',
        ]);

        $jabatan = MasterJabatan::create($request->all());

        return response()->json([
            'success' => true,
            'message' => 'Jabatan berhasil ditambahkan',
            'data' => $jabatan
        ]);
    }

    public function update(Request $request, $id)
    {
        $request->validate([
            'kode' => 'required|string|max:30|unique:master_jabatan,kode,' . $id,
            'nama' => 'required|string|max:100',
            'level' => 'required|in:kampus,fakultas,prodi',
        ]);

        $jabatan = MasterJabatan::findOrFail($id);
        $jabatan->update($request->all());

        return response()->json([
            'success' => true,
            'message' => 'Jabatan berhasil diperbarui',
            'data' => $jabatan
        ]);
    }

    public function destroy($id)
    {
        $jabatan = MasterJabatan::findOrFail($id);

        if ($jabatan->pejabat()->exists()) {
            return response()->json([
                'success' => false,
                'message' => 'Tidak dapat menghapus jabatan yang memiliki data pejabat'
            ], 422);
        }

        $jabatan->delete();

        return response()->json([
            'success' => true,
            'message' => 'Jabatan berhasil dihapus'
        ]);
    }
}
