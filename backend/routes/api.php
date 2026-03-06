<?php

use Illuminate\Support\Facades\Route;

// Auth Routes
use App\Http\Controllers\Api\Auth\AuthController;
use App\Http\Controllers\Api\ChatController;

Route::prefix('auth')->group(function () {
    Route::post('/login', [AuthController::class, 'login']);

    Route::middleware('auth:sanctum')->group(function () {
        Route::get('/user', [AuthController::class, 'user']);
        Route::post('/logout', [AuthController::class, 'logout']);
        Route::put('/profile', [AuthController::class, 'updateProfile']);
        Route::put('/password', [AuthController::class, 'changePassword']);
    });
});

// Chat Routes (all authenticated users)
Route::prefix('chat')
    ->middleware('auth:sanctum')
    ->group(function () {
        Route::get('/conversations', [ChatController::class, 'index']);
        Route::post('/conversations', [ChatController::class, 'store']);
        Route::get('/conversations/{conversation}/messages', [ChatController::class, 'messages']);
        Route::get('/admin/conversations', [ChatController::class, 'adminIndex']);
        Route::post('/conversations/{conversation}/messages', [ChatController::class, 'sendMessage']);
        Route::put('/conversations/{conversation}/read', [ChatController::class, 'markRead']);
        Route::get('/unread-count', [ChatController::class, 'unreadCount']);
        Route::get('/users', [ChatController::class, 'searchUsers']);
    });

// Admin Routes
use App\Http\Controllers\Api\Admin\DashboardController as AdminDashboardController;
use App\Http\Controllers\Api\Admin\SkripsiController as AdminSkripsiController;
use App\Http\Controllers\Api\Admin\PembimbingController as AdminPembimbingController;
use App\Http\Controllers\Api\Admin\MasterMahasiswaController;
use App\Http\Controllers\Api\Admin\MasterDosenController;
use App\Http\Controllers\Api\Admin\SeminarController as AdminSeminarController;
use App\Http\Controllers\Api\Admin\DokumenController as AdminDokumenController;
use App\Http\Controllers\Api\Admin\PdfController as AdminPdfController;
use App\Http\Controllers\Api\Admin\BimbinganController as AdminBimbinganController;
use App\Http\Controllers\Api\Admin\UjianController as AdminUjianController;
use App\Http\Controllers\Api\Admin\BeritaAcaraController as AdminBeritaAcaraController;
use App\Http\Controllers\Api\Admin\NotaBimbinganController as AdminNotaBimbinganController;
use App\Http\Controllers\Api\Admin\SKTugasController as AdminSKTugasController;
use App\Http\Controllers\Api\Admin\SKYudisiumController as AdminSKYudisiumController;
use App\Http\Controllers\Api\Admin\SeminarHasilController as AdminSeminarHasilController;
use App\Http\Controllers\Api\Admin\UserController as AdminUserController;
use App\Http\Controllers\Api\Admin\ConfigurationController as AdminConfigurationController;
use App\Http\Controllers\Api\Admin\SuperAdminController;
use App\Http\Controllers\Api\Admin\NotificationController as AdminNotificationController;
use App\Http\Controllers\Api\Admin\SkripsiVerificationController;
use App\Http\Controllers\Api\Admin\MasterProdiController;
use App\Http\Controllers\Api\Admin\MasterTahunController;
use App\Http\Controllers\Api\Admin\MasterJabatanController;
use App\Http\Controllers\Api\Admin\MasterFakultasController;
use App\Http\Controllers\Api\Admin\PeriodeJabatanController;
use App\Http\Controllers\Api\Admin\JabatanPejabatController;
use App\Http\Controllers\Api\Admin\TandaTanganController;
use App\Http\Controllers\Api\PublicConfigController;
use App\Http\Controllers\Api\VerifyDocumentController;

// Public Document Verification (no auth needed)
Route::get('/verify/{token}', [VerifyDocumentController::class, 'verify']);
Route::get('/verify/{token}/pdf', [VerifyDocumentController::class, 'pdf']);

Route::prefix('tes')->group(function () {
    Route::get('/pembimbing', [AdminPembimbingController::class, 'index']);
    Route::get('/pembimbing/available-dosen', [AdminPembimbingController::class, 'availableDosen']);
    Route::post('/pembimbing', [AdminPembimbingController::class, 'store']);
    Route::put('/pembimbing/{pembimbing}', [AdminPembimbingController::class, 'update']);
    Route::delete('/pembimbing/{pembimbing}', [AdminPembimbingController::class, 'destroy']);
});

Route::prefix('admin')
    ->middleware(['auth:sanctum', 'role:admin,super_admin,staff', 'log.activity'])
    ->group(function () {
        Route::get('/dashboard', [AdminDashboardController::class, 'index']);

        // Profile
        Route::put('/profile', function (\Illuminate\Http\Request $request) {
            $user = $request->user();
            $request->validate([
                'name' => 'sometimes|string|max:255',
                'email' => 'sometimes|email|max:255|unique:users,email,' . $user->id,
                'phone' => 'nullable|string|max:20',
                'address' => 'nullable|string|max:500',
                'password' => 'nullable|string|min:8|confirmed',
            ]);

            $data = $request->only(['name', 'email', 'phone', 'address']);
            if ($request->filled('password')) {
                $data['password'] = $request->password;
            }
            $user->update($data);

            return response()->json([
                'success' => true,
                'message' => 'Profil berhasil diperbarui',
                'data' => $user->fresh(),
            ]);
        });

        // Skripsi Verification
        Route::middleware(['role:admin,super_admin'])->group(function () {
            Route::get('/skripsi-verification', [SkripsiVerificationController::class, 'index']);
            Route::post('/skripsi-verification/{id}/approve', [SkripsiVerificationController::class, 'approve']);
            Route::post('/skripsi-verification/{id}/reject', [SkripsiVerificationController::class, 'reject']);
            Route::post('/skripsi-verification/bulk-approve', [SkripsiVerificationController::class, 'bulkApprove']);
            Route::post('/skripsi-verification/bulk-reject', [SkripsiVerificationController::class, 'bulkReject']);
        });

        // Skripsi Management
        Route::apiResource('skripsi', AdminSkripsiController::class);

        // Pembimbing Management
        Route::get('/pembimbing', [AdminPembimbingController::class, 'index']);
        Route::get('/pembimbing/available-dosen', [AdminPembimbingController::class, 'availableDosen']);
        Route::post('/pembimbing', [AdminPembimbingController::class, 'store']);
        Route::put('/pembimbing/{pembimbing}', [AdminPembimbingController::class, 'update']);
        Route::delete('/pembimbing/{pembimbing}', [AdminPembimbingController::class, 'destroy']);

        // Mahasiswa & Dosen - Read Only (accessible by admin, super_admin, staff)
        Route::get('mahasiswa', [MasterMahasiswaController::class, 'index']);
        Route::get('mahasiswa/{mahasiswa}', [MasterMahasiswaController::class, 'show']);
        Route::get('dosen', [MasterDosenController::class, 'index']);
        Route::get('dosen/{dosen}', [MasterDosenController::class, 'show']);

        // Master Data (Admin & Super Admin Only)
        Route::middleware(['role:admin,super_admin'])->group(function () {
            Route::apiResource('fakultas', MasterFakultasController::class);
            Route::apiResource('prodi', MasterProdiController::class);
            Route::apiResource('tahun', MasterTahunController::class);
            Route::apiResource('jabatan', MasterJabatanController::class);
            Route::post('mahasiswa', [MasterMahasiswaController::class, 'store']);
            Route::put('mahasiswa/{mahasiswa}', [MasterMahasiswaController::class, 'update']);
            Route::delete('mahasiswa/{mahasiswa}', [MasterMahasiswaController::class, 'destroy']);
            Route::get('mahasiswa-template', [MasterMahasiswaController::class, 'downloadTemplate']);
            Route::post('mahasiswa-import', [MasterMahasiswaController::class, 'import']);
            Route::get('mahasiswa-sync-preview', [MasterMahasiswaController::class, 'syncPreview']);
            Route::post('mahasiswa-sync-execute', [MasterMahasiswaController::class, 'syncExecute']);
            Route::post('dosen', [MasterDosenController::class, 'store']);
            Route::put('dosen/{dosen}', [MasterDosenController::class, 'update']);
            Route::delete('dosen/{dosen}', [MasterDosenController::class, 'destroy']);
            Route::get('dosen-template', [MasterDosenController::class, 'downloadTemplate']);
            Route::post('dosen-import', [MasterDosenController::class, 'import']);
            Route::get('dosen-sync-preview', [MasterDosenController::class, 'syncPreview']);
            Route::post('dosen-sync-execute', [MasterDosenController::class, 'syncExecute']);

            // User Management
            Route::get('/users', [AdminUserController::class, 'index']);
            Route::get('/users/{user}', [AdminUserController::class, 'show']);
            Route::post('/users', [AdminUserController::class, 'store']);
            Route::put('/users/{user}', [AdminUserController::class, 'update']);
            Route::post('/users/{user}/toggle-status', [AdminUserController::class, 'toggleStatus']);
            Route::post('/users/{user}/reset-password', [AdminUserController::class, 'resetPassword']);
            Route::get('/users/roles', [AdminUserController::class, 'getRoles']);
            Route::get('/users/check-email', [AdminUserController::class, 'checkEmail']);
        });

        // Seminar Management
        Route::apiResource('seminar', AdminSeminarController::class);
        Route::post('/seminar/{seminar}/berita-acara', [AdminSeminarController::class, 'createBeritaAcara']);
        Route::post('/seminar/{seminar}/penguji', [AdminSeminarController::class, 'addPenguji']);
        Route::put('/seminar/{seminar}/penguji/{penguji}', [AdminSeminarController::class, 'updatePenguji']);
        Route::delete('/seminar/{seminar}/penguji/{penguji}', [AdminSeminarController::class, 'removePenguji']);
        Route::post('/seminar/{seminar}/upload-proposal', [AdminSeminarController::class, 'uploadProposal']);

        // Seminar Hasil Management
        Route::get('/seminar-hasil', [AdminSeminarHasilController::class, 'index']);
        Route::post('/seminar-hasil', [AdminSeminarHasilController::class, 'store']);
        Route::get('/seminar-hasil/{seminar}', [AdminSeminarHasilController::class, 'show']);
        Route::put('/seminar-hasil/{seminar}', [AdminSeminarHasilController::class, 'update']);
        Route::delete('/seminar-hasil/{seminar}', [AdminSeminarHasilController::class, 'destroy']);
        Route::post('/seminar-hasil/{seminar}/berita-acara', [AdminSeminarHasilController::class, 'createBeritaAcara']);
        Route::post('/seminar-hasil/{seminar}/penguji', [AdminSeminarHasilController::class, 'addPenguji']);
        Route::put('/seminar-hasil/{seminar}/penguji/{penguji}', [AdminSeminarHasilController::class, 'updatePenguji']);
        Route::delete('/seminar-hasil/{seminar}/penguji/{penguji}', [AdminSeminarHasilController::class, 'removePenguji']);

        // Dokumen Management
        Route::apiResource('dokumen', AdminDokumenController::class);
        Route::get('/dokumen/{dokumen}/download', [AdminDokumenController::class, 'download']);

        // PDF Generation
        Route::match(['get', 'post'], '/pdf/sk-tugas/{skripsi}', [AdminPdfController::class, 'skTugas']);
        Route::get('/pdf/sk-tugas/{skripsi}/preview', [AdminPdfController::class, 'previewSkTugas']);
        Route::get('/pdf/nota-bimbingan/{skripsi}', [AdminPdfController::class, 'notaBimbingan']);
        Route::get('/pdf/berita-acara/{seminar}', [AdminPdfController::class, 'beritaAcaraSeminar']);
        Route::match(['get', 'post'], '/pdf/sk-penguji/{seminar}', [AdminPdfController::class, 'skPenguji']);

        // Bimbingan Management
        Route::get('/bimbingan', [AdminBimbinganController::class, 'index']);
        Route::get('/bimbingan/{skripsi}', [AdminBimbinganController::class, 'show']);

        // Ujian Management
        Route::get('/ujian/eligible', [AdminUjianController::class, 'eligible']);
        Route::get('/ujian', [AdminUjianController::class, 'index']);
        Route::get('/ujian/{ujian}', [AdminUjianController::class, 'show']);
        Route::post('/ujian', [AdminUjianController::class, 'store']);
        Route::put('/ujian/{ujian}', [AdminUjianController::class, 'update']);
        Route::delete('/ujian/{ujian}', [AdminUjianController::class, 'destroy']);
        Route::get('/ujian/{ujian}/available-penguji', [AdminUjianController::class, 'availablePenguji']);


        // Jadwal Ujian Export PDF
        Route::match(['get', 'post'], '/pdf/jadwal-ujian', [AdminPdfController::class, 'jadwalUjian']);

        // Berita Acara
        Route::get('/berita-acara', [AdminBeritaAcaraController::class, 'index']);
        Route::get('/berita-acara/export-excel', [AdminBeritaAcaraController::class, 'exportExcel']);
        Route::post('/berita-acara/{seminar}/generate', [AdminBeritaAcaraController::class, 'generate']);
        Route::get('/berita-acara/{seminar}/pdf', [AdminBeritaAcaraController::class, 'downloadPdf']);

        // Nota Bimbingan
        Route::get('/nota-bimbingan', [AdminNotaBimbinganController::class, 'index']);
        Route::get('/nota-bimbingan/export', [AdminNotaBimbinganController::class, 'export']);

        // SK Tugas
        Route::get('/sk-tugas', [AdminSKTugasController::class, 'index']);
        Route::put('/sk-tugas/{skripsi}', [AdminSKTugasController::class, 'update']);

        // SK Yudisium
        Route::get('/sk-yudisium', [AdminSKYudisiumController::class, 'index']);
        Route::get('/sk-yudisium/export-excel', [AdminSKYudisiumController::class, 'exportExcel']);
        Route::post('/sk-yudisium', [AdminSKYudisiumController::class, 'store']);
        Route::match(['get', 'post'], '/pdf/rekap-yudisium', [AdminPdfController::class, 'rekapYudisium']);
        Route::match(['get', 'post'], '/pdf/sk-yudisium/{skripsi}', [AdminPdfController::class, 'skYudisium']);

        // SK Yudisium Batch
        Route::get('/sk-yudisium-batch', [AdminSKYudisiumController::class, 'batchIndex']);
        Route::post('/sk-yudisium-batch', [AdminSKYudisiumController::class, 'storeBatch']);
        Route::post('/sk-yudisium-batch/assign', [AdminSKYudisiumController::class, 'assignBatch']);
        Route::delete('/sk-yudisium-batch/{id}/remove', [AdminSKYudisiumController::class, 'removeBatch']);
        Route::get('/sk-yudisium-batch/{nomor}', [AdminSKYudisiumController::class, 'batchDetail'])->where('nomor', '.*');
        Route::put('/sk-yudisium-batch/{nomor}/update', [AdminSKYudisiumController::class, 'updateBatch'])->where('nomor', '.*');
        Route::delete('/sk-yudisium-batch/{nomor}/destroy', [AdminSKYudisiumController::class, 'destroyBatch'])->where('nomor', '.*');

        // User Management
        Route::get('/users', [AdminUserController::class, 'index']);
        Route::get('/users/{user}', [AdminUserController::class, 'show']);
        Route::post('/users', [AdminUserController::class, 'store']);
        Route::put('/users/{user}', [AdminUserController::class, 'update']);
        Route::post('/users/{user}/toggle-status', [AdminUserController::class, 'toggleStatus']);
        Route::post('/users/{user}/reset-password', [AdminUserController::class, 'resetPassword']);

        // Configuration
        Route::get('/configuration/sk-tugas-signer', [AdminConfigurationController::class, 'getSkTugasSigner']);
        Route::post('/configuration/sk-tugas-signer', [AdminConfigurationController::class, 'saveSkTugasSigner']);
        Route::get('/configuration/syarat-bimbingan', [AdminConfigurationController::class, 'getSyaratBimbingan']);
        Route::post('/configuration/syarat-bimbingan', [AdminConfigurationController::class, 'saveSyaratBimbingan']);
        Route::get('/configuration/kuota-bimbingan', [AdminConfigurationController::class, 'getKuotaBimbingan']);
        Route::post('/configuration/kuota-bimbingan', [AdminConfigurationController::class, 'saveKuotaBimbingan']);
        Route::get('/configuration/tanggal-penting', [AdminConfigurationController::class, 'getTanggalPenting']);
        Route::post('/configuration/tanggal-penting', [AdminConfigurationController::class, 'saveTanggalPenting']);
        Route::get('/configuration/panduan/{type}', [AdminConfigurationController::class, 'getPanduanList']);
        Route::post('/configuration/panduan/{type}', [AdminConfigurationController::class, 'uploadPanduan']);
        Route::delete('/configuration/panduan/{id}', [AdminConfigurationController::class, 'deletePanduan']);
        Route::get('/configuration/jenis-ttd', [AdminConfigurationController::class, 'getJenisTtd']);
        Route::post('/configuration/jenis-ttd', [AdminConfigurationController::class, 'saveJenisTtd']);

        // Notifications
        Route::get('/notifications', [AdminNotificationController::class, 'index']);
        Route::get('/notifications/unread-count', [AdminNotificationController::class, 'unreadCount']);
        Route::put('/notifications/read-all', [AdminNotificationController::class, 'markAllRead']);
        Route::put('/notifications/{notification}/read', [AdminNotificationController::class, 'markAsRead']);
    });

// Public Config Routes (any authenticated user)
Route::middleware('auth:sanctum')->prefix('public')->group(function () {
    Route::get('/tanggal-penting', [PublicConfigController::class, 'getTanggalPenting']);
    Route::get('/panduan/{type}', [PublicConfigController::class, 'getPanduan']);
    Route::get('/panduan/{id}/download', [PublicConfigController::class, 'downloadPanduan']);
});

// Super Admin Routes (super_admin only)
Route::prefix('super-admin')
    ->middleware(['auth:sanctum', 'role:super_admin'])
    ->group(function () {
        Route::get('/activity-logs', [SuperAdminController::class, 'activityLogs']);
        Route::get('/activity-logs/export', [SuperAdminController::class, 'exportActivityLogs']);
        Route::get('/users', [SuperAdminController::class, 'userList']);
        Route::post('/impersonate/{user}', [SuperAdminController::class, 'impersonate']);
        Route::post('/force-logout-all', [SuperAdminController::class, 'forceLogoutAll']);
        Route::post('/toggle-system-lock', [SuperAdminController::class, 'toggleSystemLock']);
        Route::get('/system-status', [SuperAdminController::class, 'systemStatus']);
        Route::get('/trashed', [SuperAdminController::class, 'trashedRecords']);

        // Jabatan Pejabat Management
        Route::apiResource('periode-jabatan', PeriodeJabatanController::class);
        Route::apiResource('jabatan-pejabat', JabatanPejabatController::class);
        Route::get('/jabatan-pejabat-resolve', [JabatanPejabatController::class, 'resolve']);

        // Tanda Tangan Management
        Route::apiResource('tanda-tangan', TandaTanganController::class);

        // Module Settings
        Route::get('/module-settings', [SuperAdminController::class, 'getModuleSettings']);
        Route::post('/toggle-semhas', [SuperAdminController::class, 'toggleSemhasModule']);
    });

// Module settings (accessible by all authenticated users)
Route::get('/module-settings', [SuperAdminController::class, 'getModuleSettings'])
    ->middleware('auth:sanctum');

// Stop impersonate — accessible by any authenticated user (since the token belongs to the impersonated user)
Route::post('/super-admin/stop-impersonate', [SuperAdminController::class, 'stopImpersonate'])
    ->middleware('auth:sanctum');


// Dosen Routes
use App\Http\Controllers\Api\Dosen\DashboardController as DosenDashboardController;
use App\Http\Controllers\Api\Dosen\BimbinganController as DosenBimbinganController;
use App\Http\Controllers\Api\Dosen\SeminarNilaiController as DosenSeminarNilaiController;

Route::prefix('dosen')
    ->middleware(['auth:sanctum', 'role:dosen'])
    ->group(function () {
        Route::get('/dashboard', [DosenDashboardController::class, 'index']);

        // Bimbingan Management
        Route::get('/bimbingan', [DosenBimbinganController::class, 'index']);
        Route::get('/bimbingan/{skripsi}', [DosenBimbinganController::class, 'show']);
        Route::get('/bimbingan/{skripsi}/logs', [DosenBimbinganController::class, 'logs']);
        Route::put('/bimbingan/log/bulk-status', [DosenBimbinganController::class, 'bulkUpdateStatus']);
        Route::put('/bimbingan/log/{bimbingan}/status', [DosenBimbinganController::class, 'updateStatus']);

        // Jadwal (Seminar & Ujian)
        Route::get('/jadwal', [DosenBimbinganController::class, 'jadwal']);

        // Ujian Skripsi Requests (Pengajuan)
        Route::get('/ujian-requests', [DosenBimbinganController::class, 'ujianRequests']);
        Route::post('/ujian-requests/{skripsi}/respond', [DosenBimbinganController::class, 'respondUjianRequest']);

        // Seminar Detail & Nilai Input (Penguji)
        Route::get('/seminar/{seminar}', [DosenSeminarNilaiController::class, 'show']);
        Route::put('/seminar/{seminar}/nilai', [DosenSeminarNilaiController::class, 'submitNilai']);

        // Official PDF Download
        Route::get('/bimbingan/{skripsi}/pdf/{type}', [DosenBimbinganController::class, 'downloadOfficialPdf']);

        // Module settings
        Route::get('/module-settings', function () {
            return response()->json([
                'success' => true,
                'data' => [
                    'semhas_enabled' => \App\Models\SystemSetting::get('semhas_enabled', 'true') === 'true',
                ],
            ]);
        });
    });

// Mahasiswa Routes
use App\Http\Controllers\Api\Mahasiswa\DashboardController as MahasiswaDashboardController;
use App\Http\Controllers\Api\Mahasiswa\SkripsiController as MahasiswaSkripsiController;

Route::prefix('mahasiswa')
    ->middleware(['auth:sanctum', 'role:mahasiswa'])
    ->group(function () {
        Route::get('/dashboard', [MahasiswaDashboardController::class, 'index']);

        // Skripsi Management
        Route::get('/skripsi', [MahasiswaSkripsiController::class, 'index']);
        Route::post('/skripsi', [MahasiswaSkripsiController::class, 'store']);
        Route::get('/skripsi/detail', [MahasiswaSkripsiController::class, 'show']);
        Route::get('/skripsi/tahun-akademik', [MahasiswaSkripsiController::class, 'getTahunAkademikList']);
        Route::get('/skripsi/{id}/detail', [MahasiswaSkripsiController::class, 'showById']);
        Route::put('/skripsi', [MahasiswaSkripsiController::class, 'update']);
        Route::get('/skripsi/bimbingan', [MahasiswaSkripsiController::class, 'bimbingan']);
        Route::post('/skripsi/bimbingan', [MahasiswaSkripsiController::class, 'addBimbingan']);

        // Ujian Skripsi Request
        Route::get('/skripsi/ujian-eligibility', [MahasiswaSkripsiController::class, 'checkUjianEligibility']);
        Route::post('/skripsi/request-ujian', [MahasiswaSkripsiController::class, 'requestUjian']);

        // Dokumen Management
        Route::get('/skripsi/dokumen', [MahasiswaSkripsiController::class, 'getDokumen']);
        Route::post('/skripsi/dokumen', [MahasiswaSkripsiController::class, 'uploadDokumen']);
        Route::delete('/skripsi/dokumen/{dokumen}', [MahasiswaSkripsiController::class, 'deleteDokumen']);
        Route::get('/skripsi/pdf/{type}', [MahasiswaSkripsiController::class, 'downloadOfficialPdf']);
    });
