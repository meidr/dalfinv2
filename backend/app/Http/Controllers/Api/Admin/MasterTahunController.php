<?php

namespace App\Http\Controllers\Api\Admin;

use App\Http\Controllers\Controller;
use App\Models\Tahun;
use Illuminate\Http\Request;

class MasterTahunController extends Controller
{
    public function index(Request $request)
    {
        $query = Tahun::query();

        if ($request->has('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                    ->orWhere('kode', 'like', "%{$search}%");
            });
        }

        if ($request->has('active_only')) {
            $query->where('is_active', true);
        }

        $tahuns = $query->orderBy('name', 'desc')->get();

        return response()->json([
            'success' => true,
            'data' => $tahuns
        ]);
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|unique:tahuns,name',
            'kode' => 'nullable|string|max:20',
            'semester' => 'nullable|string|max:50',
            'is_active' => 'boolean',
        ]);

        $tahun = Tahun::create($request->all());

        return response()->json([
            'success' => true,
            'message' => 'Tahun berhasil ditambahkan',
            'data' => $tahun
        ]);
    }

    public function update(Request $request, $id)
    {
        $request->validate([
            'name' => 'required|string|unique:tahuns,name,' . $id,
            'kode' => 'nullable|string|max:20',
            'semester' => 'nullable|string|max:50',
            'is_active' => 'boolean',
        ]);

        $tahun = Tahun::findOrFail($id);
        $tahun->update($request->all());

        return response()->json([
            'success' => true,
            'message' => 'Tahun berhasil diperbarui',
            'data' => $tahun
        ]);
    }

    public function destroy($id)
    {
        $tahun = Tahun::findOrFail($id);

        if ($tahun->mahasiswa()->exists()) {
            return response()->json([
                'success' => false,
                'message' => 'Tidak dapat menghapus Tahun yang memiliki data mahasiswa'
            ], 422);
        }

        $tahun->delete();

        return response()->json([
            'success' => true,
            'message' => 'Tahun berhasil dihapus'
        ]);
    }
}
