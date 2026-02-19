<?php

namespace App\Http\Controllers\Api\Admin;

use App\Http\Controllers\Controller;
use App\Models\JabatanPejabat;
use App\Models\MasterJabatan;
use App\Models\PeriodeJabatan;
use Illuminate\Http\Request;

class JabatanPejabatController extends Controller
{
    public function index(Request $request)
    {
        $query = JabatanPejabat::with(['periode', 'jabatan', 'dosen', 'prodi', 'fakultas']);

        if ($request->filled('periode_id')) {
            $query->where('periode_id', $request->periode_id);
        }

        if ($request->filled('jabatan_id')) {
            $query->where('jabatan_id', $request->jabatan_id);
        }

        if ($request->filled('search')) {
            $search = $request->search;
            $query->whereHas('dosen', function ($q) use ($search) {
                $q->where('nama', 'like', "%{$search}%")
                    ->orWhere('nip', 'like', "%{$search}%");
            });
        }

        $pejabat = $query->orderBy('tgl_mulai', 'desc')->get();

        return response()->json([
            'success' => true,
            'data' => $pejabat
        ]);
    }

    public function store(Request $request)
    {
        $request->validate([
            'periode_id' => 'required|exists:periode_jabatan,id',
            'jabatan_id' => 'required|exists:master_jabatan,id',
            'dosen_id' => 'required|exists:dosen,id',
            'prodi_id' => 'nullable|exists:prodi,id',
            'fakultas_id' => 'nullable|exists:fakultas,id',
            'tgl_mulai' => 'required|date',
            'tgl_selesai' => 'nullable|date|after_or_equal:tgl_mulai',
            'is_plt' => 'boolean',
            'keterangan' => 'nullable|string|max:255',
        ]);

        $pejabat = JabatanPejabat::create($request->all());
        $pejabat->load(['periode', 'jabatan', 'dosen', 'prodi', 'fakultas']);

        return response()->json([
            'success' => true,
            'message' => 'Pejabat berhasil ditambahkan',
            'data' => $pejabat
        ]);
    }

    public function update(Request $request, $id)
    {
        $request->validate([
            'periode_id' => 'required|exists:periode_jabatan,id',
            'jabatan_id' => 'required|exists:master_jabatan,id',
            'dosen_id' => 'required|exists:dosen,id',
            'prodi_id' => 'nullable|exists:prodi,id',
            'fakultas_id' => 'nullable|exists:fakultas,id',
            'tgl_mulai' => 'required|date',
            'tgl_selesai' => 'nullable|date|after_or_equal:tgl_mulai',
            'is_plt' => 'boolean',
            'keterangan' => 'nullable|string|max:255',
        ]);

        $pejabat = JabatanPejabat::findOrFail($id);
        $pejabat->update($request->all());
        $pejabat->load(['periode', 'jabatan', 'dosen', 'prodi', 'fakultas']);

        return response()->json([
            'success' => true,
            'message' => 'Pejabat berhasil diperbarui',
            'data' => $pejabat
        ]);
    }

    public function destroy($id)
    {
        $pejabat = JabatanPejabat::findOrFail($id);
        $pejabat->delete();

        return response()->json([
            'success' => true,
            'message' => 'Pejabat berhasil dihapus'
        ]);
    }

    /**
     * Resolve active pejabat by jabatan kode and date
     *
     * Query params: kode (jabatan), tanggal, prodi_id (optional), fakultas_id (optional)
     */
    public function resolve(Request $request)
    {
        $request->validate([
            'kode' => 'required|string',
            'tanggal' => 'required|date',
            'prodi_id' => 'nullable|integer',
            'fakultas_id' => 'nullable|integer',
        ]);

        $tanggal = $request->tanggal;

        // 1. Find jabatan by kode
        $jabatan = MasterJabatan::where('kode', $request->kode)->first();
        if (!$jabatan) {
            return response()->json(['success' => false, 'message' => 'Jabatan tidak ditemukan'], 404);
        }

        // 2. Find periode that covers the date
        $periode = PeriodeJabatan::where('tgl_mulai', '<=', $tanggal)
            ->where('tgl_selesai', '>=', $tanggal)
            ->first();

        if (!$periode) {
            return response()->json(['success' => false, 'message' => 'Tidak ada periode yang cocok untuk tanggal tersebut'], 404);
        }

        // 3. Find pejabat in that periode
        $query = JabatanPejabat::with(['dosen', 'jabatan', 'prodi', 'fakultas'])
            ->where('periode_id', $periode->id)
            ->where('jabatan_id', $jabatan->id)
            ->where('tgl_mulai', '<=', $tanggal)
            ->where(function ($q) use ($tanggal) {
                $q->whereNull('tgl_selesai')
                    ->orWhere('tgl_selesai', '>=', $tanggal);
            });

        // Apply scope based on level
        if ($jabatan->level === 'prodi' && $request->filled('prodi_id')) {
            $query->where('prodi_id', $request->prodi_id);
        } elseif ($jabatan->level === 'fakultas' && $request->filled('fakultas_id')) {
            $query->where('fakultas_id', $request->fakultas_id);
        }

        $pejabat = $query->first();

        if (!$pejabat) {
            return response()->json(['success' => false, 'message' => 'Tidak ada pejabat yang aktif untuk jabatan ini'], 404);
        }

        return response()->json([
            'success' => true,
            'data' => [
                'pejabat' => $pejabat,
                'display_title' => $pejabat->display_title,
                'dosen_name' => $pejabat->dosen->full_name ?? '',
                'dosen_nip' => $pejabat->dosen->nip ?? '',
            ]
        ]);
    }
}
