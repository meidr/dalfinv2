<?php

namespace App\Http\Controllers\Api\Admin;

use App\Http\Controllers\Controller;
use App\Models\Mahasiswa;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class MasterMahasiswaController extends Controller
{
    /**
     * Display a listing of mahasiswa
     */
    public function index(Request $request)
    {
        $query = Mahasiswa::with(['prodi', 'user', 'tahun']);

        if ($request->filled('prodi_id')) {
            $query->where('prodi_id', $request->prodi_id);
        }

        if ($request->filled('tahun_id')) {
            $query->where('tahun_id', $request->tahun_id);
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
            'tahun_id' => 'required|exists:tahuns,id',
            'semester' => 'nullable|integer',
            'jenis_kelamin' => 'nullable|in:L,P',
            'no_hp' => 'nullable|string',
            'alamat' => 'nullable|string',
            'password' => 'nullable|string|min:6',
        ]);

        // Create user account
        $user = User::create([
            'name' => $request->nama,
            'email' => $request->email,
            'password' => Hash::make($request->password ?? $request->nim), // Default password = NIM
            'role' => 'mahasiswa',
            'is_active' => true,
        ]);

        // Create mahasiswa profile
        $mahasiswa = Mahasiswa::create([
            'user_id' => $user->id,
            'nim' => $request->nim,
            'nama' => $request->nama,
            'prodi_id' => $request->prodi_id,
            'tahun_id' => $request->tahun_id,
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
            'data' => $mahasiswa->load(['prodi', 'user', 'tahun'])
        ], 201);
    }

    /**
     * Display the specified mahasiswa
     */
    public function show(Mahasiswa $mahasiswa)
    {
        $mahasiswa->load(['prodi', 'user', 'tahun', 'skripsi.pembimbing.dosen']);

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
            'tahun_id' => 'sometimes|exists:tahuns,id',
            'semester' => 'sometimes|integer',
            'jenis_kelamin' => 'nullable|in:L,P',
            'no_hp' => 'nullable|string',
            'alamat' => 'nullable|string',
            'is_active' => 'sometimes|boolean',
        ]);

        $data = $request->only([
            'nama',
            'prodi_id',
            'tahun_id',
            'semester',
            'jenis_kelamin',
            'no_hp',
            'alamat',
            'is_active'
        ]);



        $mahasiswa->fill($data);
        $mahasiswa->save();

        // Update user name if changed
        if ($request->filled('nama')) {
            $mahasiswa->user->name = $request->nama;
            $mahasiswa->user->save();
        }

        return response()->json([
            'success' => true,
            'message' => 'Mahasiswa berhasil diperbarui',
            'data' => $mahasiswa->load(['prodi', 'user', 'tahun'])
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

    /**
     * Download import template
     */
    public function downloadTemplate()
    {
        $headers = ['nim', 'nama', 'email', 'tahun_id', 'prodi_id', 'jenis_kelamin', 'no_hp'];
        $example = ['2024001', 'John Doe', 'john@email.com', '1', '1', 'L', '08123456789'];

        $callback = function () use ($headers, $example) {
            $file = fopen('php://output', 'w');
            // BOM for Excel UTF-8
            fprintf($file, chr(0xEF) . chr(0xBB) . chr(0xBF));
            fputcsv($file, $headers);
            fputcsv($file, $example);
            fclose($file);
        };

        return response()->stream($callback, 200, [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => 'attachment; filename="template_mahasiswa.csv"',
        ]);
    }

    /**
     * Import mahasiswa from CSV
     */
    public function import(Request $request)
    {
        $request->validate([
            'file' => 'required|file|mimes:csv,txt|max:2048',
        ]);

        $file = $request->file('file');
        $handle = fopen($file->getRealPath(), 'r');

        // Read header
        $header = fgetcsv($handle);
        if (!$header) {
            fclose($handle);
            return response()->json(['success' => false, 'message' => 'File CSV kosong.'], 422);
        }

        // Clean BOM from first header
        $header[0] = preg_replace('/[\x{FEFF}]/u', '', $header[0]);
        $header = array_map('trim', array_map('strtolower', $header));

        $required = ['nim', 'nama', 'email', 'tahun_id', 'prodi_id'];
        foreach ($required as $col) {
            if (!in_array($col, $header)) {
                fclose($handle);
                return response()->json([
                    'success' => false,
                    'message' => "Kolom '{$col}' tidak ditemukan di file CSV.",
                ], 422);
            }
        }

        $success = 0;
        $failed = 0;
        $errors = [];
        $row = 1;

        DB::beginTransaction();
        try {
            while (($data = fgetcsv($handle)) !== false) {
                $row++;
                if (count($data) < count($header)) {
                    $errors[] = "Baris {$row}: kolom tidak lengkap";
                    $failed++;
                    continue;
                }

                $rowData = array_combine($header, $data);
                $nim = trim($rowData['nim'] ?? '');
                $nama = trim($rowData['nama'] ?? '');
                $email = trim($rowData['email'] ?? '');
                $tahunId = trim($rowData['tahun_id'] ?? '');
                $prodiId = trim($rowData['prodi_id'] ?? '');

                if (!$nim || !$nama || !$email || !$tahunId || !$prodiId) {
                    $errors[] = "Baris {$row}: data wajib tidak lengkap";
                    $failed++;
                    continue;
                }

                // Skip if NIM or email already exists
                if (Mahasiswa::where('nim', $nim)->exists()) {
                    $errors[] = "Baris {$row}: NIM '{$nim}' sudah terdaftar";
                    $failed++;
                    continue;
                }
                if (User::where('email', $email)->exists()) {
                    $errors[] = "Baris {$row}: Email '{$email}' sudah terdaftar";
                    $failed++;
                    continue;
                }

                $user = User::create([
                    'name' => $nama,
                    'email' => $email,
                    'password' => Hash::make('password'),
                    'role' => 'mahasiswa',
                    'is_active' => true,
                ]);

                Mahasiswa::create([
                    'user_id' => $user->id,
                    'nim' => $nim,
                    'nama' => $nama,
                    'prodi_id' => $prodiId,
                    'tahun_id' => $tahunId,
                    'semester' => 1,
                    'jenis_kelamin' => trim($rowData['jenis_kelamin'] ?? '') ?: null,
                    'no_hp' => trim($rowData['no_hp'] ?? '') ?: null,
                    'email' => $email,
                    'is_active' => true,
                ]);

                $success++;
            }
            DB::commit();
        } catch (\Exception $e) {
            DB::rollBack();
            fclose($handle);
            return response()->json([
                'success' => false,
                'message' => 'Import gagal: ' . $e->getMessage(),
            ], 500);
        }

        fclose($handle);

        return response()->json([
            'success' => true,
            'message' => "Import selesai: {$success} berhasil, {$failed} gagal.",
            'data' => [
                'success_count' => $success,
                'failed_count' => $failed,
                'errors' => array_slice($errors, 0, 20),
            ],
        ]);
    }
}
