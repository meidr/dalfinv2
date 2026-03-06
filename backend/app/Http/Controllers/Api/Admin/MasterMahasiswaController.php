<?php

namespace App\Http\Controllers\Api\Admin;

use App\Http\Controllers\Controller;
use App\Models\Mahasiswa;
use App\Models\Prodi;
use App\Models\Tahun;
use App\Models\User;
use App\Services\MahasiswaService;
use App\Traits\GenderFilterable;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;

class MasterMahasiswaController extends Controller
{
    use GenderFilterable;

    /**
     * Display a listing of mahasiswa
     */
    public function index(Request $request)
    {
        $query = Mahasiswa::with(['prodi', 'user', 'tahun']);

        // Gender-based filtering (direct on mahasiswa table)
        $user = $request->user();
        if ($user->role !== 'super_admin' && $user->jenis_kelamin) {
            $query->where('jenis_kelamin', $user->jenis_kelamin);
        }


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
            'email' => 'nullable|email',
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
            'username' => $request->nim,
            'email' => $request->email,
            'password' => Hash::make($request->password ?? $request->nim),
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
                if (User::where('username', $nim)->exists()) {
                    $errors[] = "Baris {$row}: NIM '{$nim}' sudah terdaftar sebagai user";
                    $failed++;
                    continue;
                }

                $user = User::create([
                    'name' => $nama,
                    'username' => $nim,
                    'email' => $email ?: null,
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

    /**
     * Preview sync data from external API (MahasiswaService::mahasiswaSkripsi)
     */
    public function syncPreview()
    {
        set_time_limit(0);

        try {
            $apiData = MahasiswaService::mahasiswaSkripsi();

            if (empty($apiData)) {
                return response()->json([
                    'success' => false,
                    'message' => 'Tidak ada data dari API atau koneksi gagal.',
                ], 422);
            }

            // Get all existing NIMs for fast lookup
            $existingMahasiswa = Mahasiswa::with(['prodi', 'tahun', 'user'])
                ->get()
                ->keyBy('nim');

            // Get existing prodi and tahun by kode
            $existingProdi = Prodi::all()->keyBy('kode');
            $existingTahun = Tahun::all()->keyBy('kode');

            $newRecords = [];
            $updateRecords = [];
            $unchangedRecords = [];
            $missingProdi = [];
            $missingTahun = [];

            foreach ($apiData as $item) {
                $nim = trim($item['nim'] ?? '');
                if (!$nim) continue;

                $nama = trim($item['nama'] ?? '');
                $email = trim($item['email'] ?? '');
                $jenisKelamin = trim($item['jenis_kelamin'] ?? '');
                $prodiKode = trim($item['kode_prodi'] ?? $item['prodi_kode'] ?? '');
                $prodiNama = trim($item['nama_prodi'] ?? $item['prodi_nama'] ?? '');
                $tahunKode = trim($item['th_akademik_kode'] ?? '');
                $tahunNama = trim($item['th_akademik_nama'] ?? $tahunKode);
                $tahunSemester = trim($item['th_akademik_semester'] ?? '');

                // Check missing prodi
                if ($prodiKode && !isset($existingProdi[$prodiKode]) && !isset($missingProdi[$prodiKode])) {
                    $missingProdi[$prodiKode] = [
                        'kode' => $prodiKode,
                        'nama' => $prodiNama ?: $prodiKode,
                    ];
                }

                // Check missing tahun
                if ($tahunKode && !isset($existingTahun[$tahunKode]) && !isset($missingTahun[$tahunKode])) {
                    $missingTahun[$tahunKode] = [
                        'kode' => $tahunKode,
                        'nama' => $tahunNama ?: $tahunKode,
                        'semester' => $tahunSemester,
                    ];
                }

                if (isset($existingMahasiswa[$nim])) {
                    $existing = $existingMahasiswa[$nim];
                    $changes = [];

                    if ($nama && $nama !== $existing->nama) {
                        $changes[] = ['field' => 'nama', 'old' => $existing->nama, 'new' => $nama];
                    }
                    if ($jenisKelamin && $jenisKelamin !== $existing->jenis_kelamin) {
                        $changes[] = ['field' => 'jenis_kelamin', 'old' => $existing->jenis_kelamin, 'new' => $jenisKelamin];
                    }
                    if ($prodiKode && $existing->prodi && $prodiKode !== $existing->prodi->kode) {
                        $changes[] = ['field' => 'prodi', 'old' => $existing->prodi->kode . ' - ' . $existing->prodi->nama, 'new' => $prodiKode . ' - ' . $prodiNama];
                    }
                    if ($tahunKode && $existing->tahun && $tahunKode !== $existing->tahun->kode) {
                        $changes[] = ['field' => 'tahun', 'old' => $existing->tahun->kode . ' - ' . $existing->tahun->name, 'new' => $tahunKode . ' - ' . $tahunNama];
                    }

                    if (count($changes) > 0) {
                        $updateRecords[] = [
                            'nim' => $nim,
                            'nama' => $nama ?: $existing->nama,
                            'prodi_kode' => $prodiKode,
                            'prodi_nama' => $prodiNama,
                            'tahun_kode' => $tahunKode,
                            'tahun_nama' => $tahunNama,
                            'tahun_semester' => $tahunSemester,
                            'jenis_kelamin' => $jenisKelamin,
                            'email' => $email,
                            'changes' => $changes,
                        ];
                    } else {
                        $unchangedRecords[] = [
                            'nim' => $nim,
                            'nama' => $existing->nama,
                        ];
                    }
                } else {
                    $newRecords[] = [
                        'nim' => $nim,
                        'nama' => $nama,
                        'prodi_kode' => $prodiKode,
                        'prodi_nama' => $prodiNama,
                        'tahun_kode' => $tahunKode,
                        'tahun_nama' => $tahunNama,
                        'tahun_semester' => $tahunSemester,
                        'jenis_kelamin' => $jenisKelamin,
                        'email' => $email,
                    ];
                }
            }

            return response()->json([
                'success' => true,
                'data' => [
                    'total_api' => count($apiData),
                    'new' => $newRecords,
                    'update' => $updateRecords,
                    'unchanged' => $unchangedRecords,
                    'new_count' => count($newRecords),
                    'update_count' => count($updateRecords),
                    'unchanged_count' => count($unchangedRecords),
                    'missing_prodi' => array_values($missingProdi),
                    'missing_tahun' => array_values($missingTahun),
                ],
            ]);
        } catch (\Exception $e) {
            Log::error('Sync preview failed: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Gagal mengambil data dari API: ' . $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Execute sync from external API data
     */
    public function syncExecute(Request $request)
    {
        set_time_limit(0);

        $request->validate([
            'items' => 'required|array|min:1',
            'items.*.nim' => 'required|string',
            'items.*.nama' => 'required|string',
            'items.*.action' => 'required|in:create,update',
        ]);

        $items = $request->input('items');
        $successCount = 0;
        $failedCount = 0;
        $errors = [];

        // Build lookup caches for prodi and tahun by kode
        $prodiCache = Prodi::all()->keyBy('kode');
        $tahunCache = Tahun::all()->keyBy('kode');

        // Process in chunks to avoid memory and timeout issues
        $chunks = array_chunk($items, 200);

        foreach ($chunks as $chunk) {
            DB::beginTransaction();
            try {
                foreach ($chunk as $item) {
                    try {
                        $nim = trim($item['nim']);
                        $nama = trim($item['nama']);
                        $email = trim($item['email'] ?? '');
                        $jenisKelamin = trim($item['jenis_kelamin'] ?? '') ?: null;
                        $prodiKode = trim($item['prodi_kode'] ?? '');
                        $prodiNama = trim($item['prodi_nama'] ?? '');
                        $tahunKode = trim($item['tahun_kode'] ?? '');
                        $tahunNama = trim($item['tahun_nama'] ?? '');
                        $tahunSemester = trim($item['tahun_semester'] ?? '');

                        // Resolve prodi — auto-create if missing
                        $prodiId = null;
                        if ($prodiKode) {
                            if (!isset($prodiCache[$prodiKode])) {
                                $newProdi = Prodi::create([
                                    'kode' => $prodiKode,
                                    'nama' => $prodiNama ?: $prodiKode,
                                    'is_active' => true,
                                ]);
                                $prodiCache[$prodiKode] = $newProdi;
                            }
                            $prodiId = $prodiCache[$prodiKode]->id;
                        }

                        // Resolve tahun — auto-create if missing
                        $tahunId = null;
                        if ($tahunKode) {
                            if (!isset($tahunCache[$tahunKode])) {
                                $newTahun = Tahun::create([
                                    'kode' => $tahunKode,
                                    'name' => $tahunNama ?: $tahunKode,
                                    'semester' => $tahunSemester ?: null,
                                    'is_active' => true,
                                ]);
                                $tahunCache[$tahunKode] = $newTahun;
                            }
                            $tahunId = $tahunCache[$tahunKode]->id;
                        }

                        if ($item['action'] === 'create') {
                            // Create User
                            if (!User::where('username', $nim)->exists()) {
                                $user = User::create([
                                    'name' => $nama,
                                    'username' => $nim,
                                    'email' => $email ?: null,
                                    'password' => Hash::make($nim),
                                    'role' => 'mahasiswa',
                                    'is_active' => true,
                                ]);

                                Mahasiswa::create([
                                    'user_id' => $user->id,
                                    'nim' => $nim,
                                    'nama' => $nama,
                                    'jenis_kelamin' => $jenisKelamin,
                                    'prodi_id' => $prodiId,
                                    'tahun_id' => $tahunId,
                                ]);

                                $successCount++;
                            } else {
                                $errors[] = "NIM {$nim}: User sudah terdaftar.";
                                $failedCount++;
                            }
                        } elseif ($item['action'] === 'update') {
                            $mahasiswa = Mahasiswa::where('nim', $nim)->first();
                            if (!$mahasiswa) {
                                $errors[] = "NIM {$nim}: Data mahasiswa tidak ditemukan.";
                                $failedCount++;
                                continue;
                            }

                            if ($nama) $mahasiswa->nama = $nama;
                            if ($jenisKelamin) $mahasiswa->jenis_kelamin = $jenisKelamin;
                            if ($prodiId) $mahasiswa->prodi_id = $prodiId;
                            if ($tahunId) $mahasiswa->tahun_id = $tahunId;
                            $mahasiswa->save();

                            // Update user name
                            if ($nama && $mahasiswa->user) {
                                $mahasiswa->user->name = $nama;
                                $mahasiswa->user->save();
                            }

                            $successCount++;
                        }
                    } catch (\Exception $e) {
                        $nim = $item['nim'] ?? '?';
                        $errors[] = "NIM {$nim}: " . $e->getMessage();
                        $failedCount++;
                    }
                }

                DB::commit();
            } catch (\Exception $e) {
                DB::rollBack();
                Log::error('Sync chunk failed: ' . $e->getMessage());
                $errors[] = 'Chunk gagal: ' . $e->getMessage();
                $failedCount += count($chunk);
            }
        }

        return response()->json([
            'success' => true,
            'message' => "Sinkronisasi selesai: {$successCount} berhasil, {$failedCount} gagal.",
            'data' => [
                'success_count' => $successCount,
                'failed_count' => $failedCount,
                'errors' => array_slice($errors, 0, 50),
            ],
        ]);
    }
}
