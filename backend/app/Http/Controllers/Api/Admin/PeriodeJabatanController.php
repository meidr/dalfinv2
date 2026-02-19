<?php

namespace App\Http\Controllers\Api\Admin;

use App\Http\Controllers\Controller;
use App\Models\PeriodeJabatan;
use Illuminate\Http\Request;

class PeriodeJabatanController extends Controller
{
    public function index(Request $request)
    {
        $query = PeriodeJabatan::withCount('pejabat');

        if ($request->filled('active_only')) {
            $query->where('is_active', true);
        }

        $periodes = $query->orderBy('tgl_mulai', 'desc')->get();

        return response()->json([
            'success' => true,
            'data' => $periodes
        ]);
    }

    public function store(Request $request)
    {
        $request->validate([
            'nama' => 'required|string|max:50',
            'tgl_mulai' => 'required|date',
            'tgl_selesai' => 'required|date|after:tgl_mulai',
            'is_active' => 'boolean',
        ]);

        // If setting as active, deactivate others
        if ($request->is_active) {
            PeriodeJabatan::where('is_active', true)->update(['is_active' => false]);
        }

        $periode = PeriodeJabatan::create($request->all());

        return response()->json([
            'success' => true,
            'message' => 'Periode berhasil ditambahkan',
            'data' => $periode
        ]);
    }

    public function update(Request $request, $id)
    {
        $request->validate([
            'nama' => 'required|string|max:50',
            'tgl_mulai' => 'required|date',
            'tgl_selesai' => 'required|date|after:tgl_mulai',
            'is_active' => 'boolean',
        ]);

        $periode = PeriodeJabatan::findOrFail($id);

        // If setting as active, deactivate others
        if ($request->is_active && !$periode->is_active) {
            PeriodeJabatan::where('is_active', true)->update(['is_active' => false]);
        }

        $periode->update($request->all());

        return response()->json([
            'success' => true,
            'message' => 'Periode berhasil diperbarui',
            'data' => $periode
        ]);
    }

    public function destroy($id)
    {
        $periode = PeriodeJabatan::findOrFail($id);

        if ($periode->pejabat()->exists()) {
            return response()->json([
                'success' => false,
                'message' => 'Tidak dapat menghapus periode yang memiliki data pejabat'
            ], 422);
        }

        $periode->delete();

        return response()->json([
            'success' => true,
            'message' => 'Periode berhasil dihapus'
        ]);
    }
}
