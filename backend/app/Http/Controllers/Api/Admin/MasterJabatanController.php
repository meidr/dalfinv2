<?php

namespace App\Http\Controllers\Api\Admin;

use App\Http\Controllers\Controller;
use App\Models\Jabatan;
use Illuminate\Http\Request;

class MasterJabatanController extends Controller
{
    public function index(Request $request)
    {
        $query = Jabatan::query();

        if ($request->has('search')) {
            $search = $request->search;
            $query->where('name', 'like', "%{$search}%");
        }

        $jabatans = $query->orderBy('name', 'asc')->get();

        return response()->json([
            'success' => true,
            'data' => $jabatans
        ]);
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|unique:jabatans,name',
        ]);

        $jabatan = Jabatan::create($request->all());

        return response()->json([
            'success' => true,
            'message' => 'Jabatan berhasil ditambahkan',
            'data' => $jabatan
        ]);
    }

    public function update(Request $request, $id)
    {
        $request->validate([
            'name' => 'required|string|unique:jabatans,name,' . $id,
        ]);

        $jabatan = Jabatan::findOrFail($id);
        $jabatan->update($request->all());

        return response()->json([
            'success' => true,
            'message' => 'Jabatan berhasil diperbarui',
            'data' => $jabatan
        ]);
    }

    public function destroy($id)
    {
        $jabatan = Jabatan::findOrFail($id);

        if ($jabatan->dosen()->exists()) {
            return response()->json([
                'success' => false,
                'message' => 'Tidak dapat menghapus Jabatan yang memiliki data dosen'
            ], 422);
        }

        $jabatan->delete();

        return response()->json([
            'success' => true,
            'message' => 'Jabatan berhasil dihapus'
        ]);
    }
}
