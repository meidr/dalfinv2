<?php

namespace App\Http\Controllers\Api\Admin;

use App\Http\Controllers\Controller;
use App\Models\Configuration;
use App\Models\Dosen;
use App\Models\Prodi;
use App\Models\User;
use App\Services\DosenService;
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
        // Get global kuota default
        $globalConfig = Configuration::where('key', 'kuota_bimbingan_default')->first();
        $globalKuota = $globalConfig ? ($globalConfig->value['kuota'] ?? 10) : 10;

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
                    ->orWhere('nip', 'like', "%{$search}%")
                    ->orWhere('nidn', 'like', "%{$search}%");
            });
        }

        if ($request->filled('status')) {
            $isActive = $request->status === 'aktif';
            $query->whereHas('user', function ($q) use ($isActive) {
                $q->where('is_active', $isActive);
            });
        }

        $perPage = $request->get('per_page', 15);
        $dosen = $query->orderBy('nama', 'asc')->paginate($perPage);

        // Add jumlah_bimbingan and apply global kuota default
        $dosen->getCollection()->transform(function ($item) use ($globalKuota) {
            $item->jumlah_bimbingan = $item->current_bimbingan;
            if (!$item->kuota_bimbingan) {
                $item->kuota_bimbingan = $globalKuota;
            }
            return $item;
        });

        return response()->json([
            'success' => true,
            'data' => $dosen,
            'global_kuota' => $globalKuota,
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
            'email' => 'nullable|email',
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
            'username' => $request->nip,
            'email' => $request->email,
            'password' => Hash::make($request->nip),
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
            'nama',
            'prodi_id',
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
                if (User::where('username', $nip)->exists()) {
                    $errors[] = "Baris {$row}: NIP '{$nip}' sudah terdaftar sebagai user";
                    $failed++;
                    continue;
                }

                $user = User::create([
                    'name' => $nama,
                    'username' => $nip,
                    'email' => $email ?: null,
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

    /**
     * Preview sync data from external API (DosenService::all)
     */
    public function syncPreview()
    {
        set_time_limit(0);

        try {
            $apiData = DosenService::all();

            if (empty($apiData)) {
                return response()->json([
                    'success' => false,
                    'message' => 'Tidak ada data dari API atau koneksi gagal.',
                ], 422);
            }

            // Get all existing dosen by NIP
            $existingDosen = Dosen::with(['prodi', 'user'])->get()->keyBy('nip');
            $existingProdi = Prodi::all()->keyBy('kode');

            $newRecords = [];
            $updateRecords = [];
            $unchangedRecords = [];
            $missingProdi = [];

            foreach ($apiData as $item) {
                $kode = trim($item['kode'] ?? '');
                $nip = trim($item['nip'] ?? '');
                if (!$kode) continue;

                $nama = trim($item['nama'] ?? '');
                $email = trim($item['email'] ?? '');
                $jenisKelamin = trim($item['jenis_kelamin'] ?? '');
                $gelarDepan = trim($item['gelar_depan'] ?? '');
                $gelarBelakang = trim($item['gelar_belakang'] ?? '');
                $jabatanFungsional = trim($item['jabatan_fungsional'] ?? '');
                $prodiKode = trim($item['kode_prodi'] ?? $item['prodi_kode'] ?? '');
                $prodiNama = trim($item['nama_prodi'] ?? $item['prodi_nama'] ?? '');

                // Check missing prodi
                if ($prodiKode && !isset($existingProdi[$prodiKode]) && !isset($missingProdi[$prodiKode])) {
                    $missingProdi[$prodiKode] = [
                        'kode' => $prodiKode,
                        'nama' => $prodiNama ?: $prodiKode,
                    ];
                }

                $lookupKey = $nip ?: $kode;
                if (isset($existingDosen[$lookupKey])) {
                    $existing = $existingDosen[$lookupKey];
                    $changes = [];

                    if ($nama && $nama !== $existing->nama) {
                        $changes[] = ['field' => 'nama', 'old' => $existing->nama, 'new' => $nama];
                    }
                    if ($jenisKelamin && $jenisKelamin !== $existing->jenis_kelamin) {
                        $changes[] = ['field' => 'jenis_kelamin', 'old' => $existing->jenis_kelamin, 'new' => $jenisKelamin];
                    }
                    if ($gelarDepan && $gelarDepan !== $existing->gelar_depan) {
                        $changes[] = ['field' => 'gelar_depan', 'old' => $existing->gelar_depan, 'new' => $gelarDepan];
                    }
                    if ($gelarBelakang && $gelarBelakang !== $existing->gelar_belakang) {
                        $changes[] = ['field' => 'gelar_belakang', 'old' => $existing->gelar_belakang, 'new' => $gelarBelakang];
                    }
                    if ($jabatanFungsional && $jabatanFungsional !== $existing->jabatan_fungsional) {
                        $changes[] = ['field' => 'jabatan_fungsional', 'old' => $existing->jabatan_fungsional, 'new' => $jabatanFungsional];
                    }
                    if ($prodiKode && $existing->prodi && $prodiKode !== $existing->prodi->kode) {
                        $changes[] = ['field' => 'prodi', 'old' => $existing->prodi->kode . ' - ' . $existing->prodi->nama, 'new' => $prodiKode . ' - ' . $prodiNama];
                    }

                    if (count($changes) > 0) {
                        $updateRecords[] = [
                            'kode' => $kode,
                            'nip' => $nip,
                            'nama' => $nama ?: $existing->nama,
                            'email' => $email,
                            'jenis_kelamin' => $jenisKelamin,
                            'gelar_depan' => $gelarDepan,
                            'gelar_belakang' => $gelarBelakang,
                            'jabatan_fungsional' => $jabatanFungsional,
                            'prodi_kode' => $prodiKode,
                            'prodi_nama' => $prodiNama,
                            'changes' => $changes,
                        ];
                    } else {
                        $unchangedRecords[] = [
                            'kode' => $kode,
                            'nip' => $nip,
                            'nama' => $existing->nama,
                        ];
                    }
                } else {
                    $newRecords[] = [
                        'kode' => $kode,
                        'nip' => $nip,
                        'nama' => $nama,
                        'email' => $email,
                        'jenis_kelamin' => $jenisKelamin,
                        'gelar_depan' => $gelarDepan,
                        'gelar_belakang' => $gelarBelakang,
                        'jabatan_fungsional' => $jabatanFungsional,
                        'prodi_kode' => $prodiKode,
                        'prodi_nama' => $prodiNama,
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
                ],
            ]);
        } catch (\Exception $e) {
            \Log::error('Dosen sync preview failed: ' . $e->getMessage());
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
            'items.*.kode' => 'required|string',
            'items.*.nama' => 'required|string',
            'items.*.action' => 'required|in:create,update',
        ]);

        $items = $request->input('items');
        $successCount = 0;
        $failedCount = 0;
        $errors = [];

        $prodiCache = Prodi::all()->keyBy('kode');

        $chunks = array_chunk($items, 200);

        foreach ($chunks as $chunk) {
            DB::beginTransaction();
            try {
                foreach ($chunk as $item) {
                    try {
                        $kode = trim($item['kode']);
                        $nip = trim($item['nip'] ?? '') ?: $kode;
                        $nama = trim($item['nama']);
                        $email = trim($item['email'] ?? '');
                        $jenisKelamin = trim($item['jenis_kelamin'] ?? '') ?: null;
                        $gelarDepan = trim($item['gelar_depan'] ?? '') ?: null;
                        $gelarBelakang = trim($item['gelar_belakang'] ?? '') ?: null;
                        $jabatanFungsional = trim($item['jabatan_fungsional'] ?? '') ?: null;
                        $prodiKode = trim($item['prodi_kode'] ?? '');
                        $prodiNama = trim($item['prodi_nama'] ?? '');

                        // Resolve prodi
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

                        if ($item['action'] === 'create') {
                            if (!User::where('username', $kode)->exists()) {
                                $user = User::create([
                                    'name' => $nama,
                                    'username' => $kode,
                                    'email' => $email ?: null,
                                    'password' => Hash::make($kode),
                                    'role' => 'dosen',
                                    'jenis_kelamin' => $jenisKelamin,
                                    'is_active' => true,
                                ]);

                                Dosen::create([
                                    'user_id' => $user->id,
                                    'nip' => $nip,
                                    'nama' => $nama,
                                    'gelar_depan' => $gelarDepan,
                                    'gelar_belakang' => $gelarBelakang,
                                    'jabatan_fungsional' => $jabatanFungsional,
                                    'email' => $email ?: null,
                                    'jenis_kelamin' => $jenisKelamin,
                                    'prodi_id' => $prodiId,
                                    'kuota_bimbingan' => 10,
                                    'is_active' => true,
                                ]);

                                $successCount++;
                            } else {
                                $errors[] = "Kode {$kode}: User sudah terdaftar.";
                                $failedCount++;
                            }
                        } elseif ($item['action'] === 'update') {
                            $dosen = Dosen::where('nip', $nip)->first()
                                ?? Dosen::where('nip', $kode)->first();

                            if (!$dosen) {
                                $errors[] = "Kode {$kode}: Data dosen tidak ditemukan.";
                                $failedCount++;
                                continue;
                            }

                            if ($nama) $dosen->nama = $nama;
                            if ($jenisKelamin) $dosen->jenis_kelamin = $jenisKelamin;
                            if ($gelarDepan) $dosen->gelar_depan = $gelarDepan;
                            if ($gelarBelakang) $dosen->gelar_belakang = $gelarBelakang;
                            if ($jabatanFungsional) $dosen->jabatan_fungsional = $jabatanFungsional;
                            if ($prodiId) $dosen->prodi_id = $prodiId;
                            if ($email) $dosen->email = $email;
                            $dosen->save();

                            if ($nama && $dosen->user) {
                                $dosen->user->name = $nama;
                                $dosen->user->save();
                            }

                            $successCount++;
                        }
                    } catch (\Exception $e) {
                        $kode = $item['kode'] ?? '?';
                        $errors[] = "Kode {$kode}: " . $e->getMessage();
                        $failedCount++;
                    }
                }

                DB::commit();
            } catch (\Exception $e) {
                DB::rollBack();
                \Log::error('Dosen sync chunk failed: ' . $e->getMessage());
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
