<?php

namespace App\Http\Controllers\Api\Admin;

use App\Http\Controllers\Controller;
use App\Models\Prodi;
use Illuminate\Http\Request;

class MasterProdiController extends Controller
{
    public function index(Request $request)
    {
        $query = Prodi::query();

        if ($request->has('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('nama', 'like', "%{$search}%")
                    ->orWhere('kode', 'like', "%{$search}%");
            });
        }

        if ($request->has('active_only')) {
            $query->where('is_active', true);
        }

        $prodis = $query->orderBy('nama', 'asc')->get();

        return response()->json([
            'success' => true,
            'data' => $prodis
        ]);
    }

    public function store(Request $request)
    {
        $request->validate([
            'kode' => 'required|string|unique:prodi,kode',
            'nama' => 'required|string',
            'jenjang' => 'required|string',
            'fakultas' => 'nullable|string',
        ]);

        $prodi = Prodi::create($request->all());

        return response()->json([
            'success' => true,
            'message' => 'Program Studi berhasil ditambahkan',
            'data' => $prodi
        ]);
    }

    public function update(Request $request, $id)
    {
        $request->validate([
            'kode' => 'required|string|unique:prodi,kode,' . $id,
            'nama' => 'required|string',
            'jenjang' => 'required|string',
        ]);

        $prodi = Prodi::findOrFail($id);
        $prodi->update($request->all());

        return response()->json([
            'success' => true,
            'message' => 'Program Studi berhasil diperbarui',
            'data' => $prodi
        ]);
    }

    public function destroy($id)
    {
        $prodi = Prodi::findOrFail($id);

        if ($prodi->mahasiswa()->exists() || $prodi->dosen()->exists()) {
            return response()->json([
                'success' => false,
                'message' => 'Tidak dapat menghapus Prodi yang memiliki data mahasiswa atau dosen'
            ], 422);
        }

        $prodi->delete();

        return response()->json([
            'success' => true,
            'message' => 'Program Studi berhasil dihapus'
        ]);
    }
}
