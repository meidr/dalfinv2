<?php

namespace App\Http\Controllers\Api\Admin;

use App\Http\Controllers\Controller;
use App\Models\Dosen;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class MasterDosenController extends Controller
{
    /**
     * Display a listing of dosen
     */
    public function index(Request $request)
    {
        $query = Dosen::with(['prodi', 'user', 'jabatan'])
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
            'jabatan_id' => 'nullable|exists:jabatans,id',
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
            'jabatan_id' => $request->jabatan_id,
            'email' => $request->email,
            'no_hp' => $request->no_hp,
            'jenis_kelamin' => $request->jenis_kelamin,
            'kuota_bimbingan' => $request->kuota_bimbingan ?? 10,
            'is_active' => true,
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Dosen berhasil ditambahkan',
            'data' => $dosen->load(['prodi', 'user', 'jabatan'])
        ], 201);
    }

    /**
     * Display the specified dosen
     */
    public function show(Dosen $dosen)
    {
        $dosen->load(['prodi', 'user', 'jabatan', 'pembimbing.skripsi.mahasiswa']);

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
            'jabatan_id' => 'nullable|exists:jabatans,id',
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
            'nama',
            'prodi_id',
            'jabatan_id',
            'gelar_depan',
            'gelar_belakang',
            'jabatan_fungsional',
            'bidang_keahlian',
            'no_hp',
            'jenis_kelamin',
            'kuota_bimbingan',
            'is_active'
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

    /**
     * Download import template
     */
    public function downloadTemplate()
    {
        $headers = ['nip', 'nama', 'email', 'prodi_id', 'gelar_depan', 'gelar_belakang', 'jenis_kelamin', 'no_hp'];
        $example = ['198501012020011001', 'Budi Santoso', 'budi@email.com', '1', 'Dr.', 'M.Kom.', 'L', '08123456789'];

        $callback = function () use ($headers, $example) {
            $file = fopen('php://output', 'w');
            fprintf($file, chr(0xEF) . chr(0xBB) . chr(0xBF));
            fputcsv($file, $headers);
            fputcsv($file, $example);
            fclose($file);
        };

        return response()->stream($callback, 200, [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => 'attachment; filename="template_dosen.csv"',
        ]);
    }

    /**
     * Import dosen from CSV
     */
    public function import(Request $request)
    {
        $request->validate([
            'file' => 'required|file|mimes:csv,txt|max:2048',
        ]);

        $file = $request->file('file');
        $handle = fopen($file->getRealPath(), 'r');

        $header = fgetcsv($handle);
        if (!$header) {
            fclose($handle);
            return response()->json(['success' => false, 'message' => 'File CSV kosong.'], 422);
        }

        $header[0] = preg_replace('/[\x{FEFF}]/u', '', $header[0]);
        $header = array_map('trim', array_map('strtolower', $header));

        $required = ['nip', 'nama', 'email'];
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
                $nip = trim($rowData['nip'] ?? '');
                $nama = trim($rowData['nama'] ?? '');
                $email = trim($rowData['email'] ?? '');

                if (!$nip || !$nama || !$email) {
                    $errors[] = "Baris {$row}: data wajib tidak lengkap (nip, nama, email)";
                    $failed++;
                    continue;
                }

                if (Dosen::where('nip', $nip)->exists()) {
                    $errors[] = "Baris {$row}: NIP '{$nip}' sudah terdaftar";
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
                    'password' => Hash::make($nip),
                    'role' => 'dosen',
                    'is_active' => true,
                ]);

                Dosen::create([
                    'user_id' => $user->id,
                    'nip' => $nip,
                    'nama' => $nama,
                    'gelar_depan' => trim($rowData['gelar_depan'] ?? '') ?: null,
                    'gelar_belakang' => trim($rowData['gelar_belakang'] ?? '') ?: null,
                    'prodi_id' => trim($rowData['prodi_id'] ?? '') ?: null,
                    'email' => $email,
                    'jenis_kelamin' => trim($rowData['jenis_kelamin'] ?? '') ?: null,
                    'no_hp' => trim($rowData['no_hp'] ?? '') ?: null,
                    'kuota_bimbingan' => 10,
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
