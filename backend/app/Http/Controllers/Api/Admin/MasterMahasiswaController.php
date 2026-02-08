<?php

namespace App\Http\Controllers\Api\Admin;

use App\Http\Controllers\Controller;
use App\Models\Mahasiswa;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class MasterMahasiswaController extends Controller
{
    /**
     * Display a listing of mahasiswa
     */
    public function index(Request $request)
    {
        $query = Mahasiswa::with(['prodi', 'user']);

        if ($request->filled('prodi_id')) {
            $query->where('prodi_id', $request->prodi_id);
        }

        if ($request->filled('angkatan')) {
            $query->where('angkatan', $request->angkatan);
        }

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('nama', 'like', "%{$search}%")
                    ->orWhere('nim', 'like', "%{$search}%");
            });
        }

        $perPage = $request->get('per_page', 15);
        $mahasiswa = $query->orderBy('nim', 'desc')->paginate($perPage);

        return response()->json([
            'success' => true,
            'data' => $mahasiswa
        ]);
    }

    /**
     * Store a newly created mahasiswa
     */
    public function store(Request $request)
    {
        $request->validate([
            'nim' => 'required|string|unique:mahasiswa,nim',
            'nama' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email',
            'prodi_id' => 'required|exists:prodi,id',
            'angkatan' => 'required|string',
            'semester' => 'nullable|integer',
            'jenis_kelamin' => 'nullable|in:L,P',
            'no_hp' => 'nullable|string',
            'alamat' => 'nullable|string',
        ]);

        // Create user account
        $user = User::create([
            'name' => $request->nama,
            'email' => $request->email,
            'password' => Hash::make($request->nim), // Default password = NIM
            'role' => 'mahasiswa',
            'is_active' => true,
        ]);

        // Create mahasiswa profile
        $mahasiswa = Mahasiswa::create([
            'user_id' => $user->id,
            'nim' => $request->nim,
            'nama' => $request->nama,
            'prodi_id' => $request->prodi_id,
            'angkatan' => $request->angkatan,
            'semester' => $request->semester ?? 1,
            'jenis_kelamin' => $request->jenis_kelamin,
            'no_hp' => $request->no_hp,
            'alamat' => $request->alamat,
            'email' => $request->email,
            'is_active' => true,
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Mahasiswa berhasil ditambahkan',
            'data' => $mahasiswa->load(['prodi', 'user'])
        ], 201);
    }

    /**
     * Display the specified mahasiswa
     */
    public function show(Mahasiswa $mahasiswa)
    {
        $mahasiswa->load(['prodi', 'user', 'skripsi.pembimbing.dosen']);

        return response()->json([
            'success' => true,
            'data' => $mahasiswa
        ]);
    }

    /**
     * Update the specified mahasiswa
     */
    public function update(Request $request, Mahasiswa $mahasiswa)
    {
        $request->validate([
            'nama' => 'sometimes|string|max:255',
            'prodi_id' => 'sometimes|exists:prodi,id',
            'semester' => 'sometimes|integer',
            'jenis_kelamin' => 'nullable|in:L,P',
            'no_hp' => 'nullable|string',
            'alamat' => 'nullable|string',
            'is_active' => 'sometimes|boolean',
        ]);

        $mahasiswa->fill($request->only([
            'nama', 'prodi_id', 'semester', 'jenis_kelamin', 'no_hp', 'alamat', 'is_active'
        ]));
        $mahasiswa->save();

        // Update user name if changed
        if ($request->filled('nama')) {
            $mahasiswa->user->name = $request->nama;
            $mahasiswa->user->save();
        }

        return response()->json([
            'success' => true,
            'message' => 'Mahasiswa berhasil diperbarui',
            'data' => $mahasiswa->load(['prodi', 'user'])
        ]);
    }

    /**
     * Remove the specified mahasiswa
     */
    public function destroy(Mahasiswa $mahasiswa)
    {
        $mahasiswa->is_active = false;
        $mahasiswa->save();

        $mahasiswa->user->is_active = false;
        $mahasiswa->user->save();

        return response()->json([
            'success' => true,
            'message' => 'Mahasiswa berhasil dihapus'
        ]);
    }
}
