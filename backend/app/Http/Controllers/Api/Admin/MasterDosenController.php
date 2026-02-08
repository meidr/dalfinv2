<?php

namespace App\Http\Controllers\Api\Admin;

use App\Http\Controllers\Controller;
use App\Models\Dosen;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class MasterDosenController extends Controller
{
    /**
     * Display a listing of dosen
     */
    public function index(Request $request)
    {
        $query = Dosen::with(['prodi', 'user'])
            ->withCount(['pembimbing as current_bimbingan' => function ($q) {
                $q->where('is_active', true);
            }]);

        if ($request->filled('prodi_id')) {
            $query->where('prodi_id', $request->prodi_id);
        }

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('nama', 'like', "%{$search}%")
                    ->orWhere('nip', 'like', "%{$search}%");
            });
        }

        $perPage = $request->get('per_page', 15);
        $dosen = $query->orderBy('nama', 'asc')->paginate($perPage);

        return response()->json([
            'success' => true,
            'data' => $dosen
        ]);
    }

    /**
     * Store a newly created dosen
     */
    public function store(Request $request)
    {
        $request->validate([
            'nip' => 'required|string|unique:dosen,nip',
            'nama' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email',
            'prodi_id' => 'nullable|exists:prodi,id',
            'gelar_depan' => 'nullable|string',
            'gelar_belakang' => 'nullable|string',
            'jabatan_fungsional' => 'nullable|string',
            'bidang_keahlian' => 'nullable|string',
            'no_hp' => 'nullable|string',
            'jenis_kelamin' => 'nullable|in:L,P',
            'kuota_bimbingan' => 'nullable|integer|min:1',
        ]);

        // Create user account
        $user = User::create([
            'name' => $request->nama,
            'email' => $request->email,
            'password' => Hash::make($request->nip), // Default password = NIP
            'role' => 'dosen',
            'is_active' => true,
        ]);

        // Create dosen profile
        $dosen = Dosen::create([
            'user_id' => $user->id,
            'nip' => $request->nip,
            'nama' => $request->nama,
            'gelar_depan' => $request->gelar_depan,
            'gelar_belakang' => $request->gelar_belakang,
            'jabatan_fungsional' => $request->jabatan_fungsional,
            'bidang_keahlian' => $request->bidang_keahlian,
            'prodi_id' => $request->prodi_id,
            'email' => $request->email,
            'no_hp' => $request->no_hp,
            'jenis_kelamin' => $request->jenis_kelamin,
            'kuota_bimbingan' => $request->kuota_bimbingan ?? 10,
            'is_active' => true,
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Dosen berhasil ditambahkan',
            'data' => $dosen->load(['prodi', 'user'])
        ], 201);
    }

    /**
     * Display the specified dosen
     */
    public function show(Dosen $dosen)
    {
        $dosen->load(['prodi', 'user', 'pembimbing.skripsi.mahasiswa']);

        return response()->json([
            'success' => true,
            'data' => $dosen
        ]);
    }

    /**
     * Update the specified dosen
     */
    public function update(Request $request, Dosen $dosen)
    {
        $request->validate([
            'nama' => 'sometimes|string|max:255',
            'prodi_id' => 'nullable|exists:prodi,id',
            'gelar_depan' => 'nullable|string',
            'gelar_belakang' => 'nullable|string',
            'jabatan_fungsional' => 'nullable|string',
            'bidang_keahlian' => 'nullable|string',
            'no_hp' => 'nullable|string',
            'jenis_kelamin' => 'nullable|in:L,P',
            'kuota_bimbingan' => 'sometimes|integer|min:1',
            'is_active' => 'sometimes|boolean',
        ]);

        $dosen->fill($request->only([
            'nama', 'prodi_id', 'gelar_depan', 'gelar_belakang',
            'jabatan_fungsional', 'bidang_keahlian', 'no_hp',
            'jenis_kelamin', 'kuota_bimbingan', 'is_active'
        ]));
        $dosen->save();

        if ($request->filled('nama')) {
            $dosen->user->name = $request->nama;
            $dosen->user->save();
        }

        return response()->json([
            'success' => true,
            'message' => 'Dosen berhasil diperbarui',
            'data' => $dosen->load(['prodi', 'user'])
        ]);
    }

    /**
     * Remove the specified dosen
     */
    public function destroy(Dosen $dosen)
    {
        $dosen->is_active = false;
        $dosen->save();

        $dosen->user->is_active = false;
        $dosen->user->save();

        return response()->json([
            'success' => true,
            'message' => 'Dosen berhasil dihapus'
        ]);
    }
}
