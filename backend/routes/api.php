<?php

use Illuminate\Support\Facades\Route;

// Auth Routes
use App\Http\Controllers\Api\Auth\AuthController;

Route::prefix('auth')->group(function () {
    Route::post('/login', [AuthController::class, 'login']);

    Route::middleware('auth:sanctum')->group(function () {
        Route::get('/user', [AuthController::class, 'user']);
        Route::post('/logout', [AuthController::class, 'logout']);
        Route::put('/profile', [AuthController::class, 'updateProfile']);
        Route::put('/password', [AuthController::class, 'changePassword']);
    });
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
use App\Http\Controllers\Api\Admin\UserController as AdminUserController;

Route::prefix('admin')
    ->middleware(['auth:sanctum', 'role:admin'])
    ->group(function () {
        Route::get('/dashboard', [AdminDashboardController::class, 'index']);

        // Skripsi Management
        Route::apiResource('skripsi', AdminSkripsiController::class);

        // Pembimbing Management
        Route::get('/pembimbing', [AdminPembimbingController::class, 'index']);
        Route::get('/pembimbing/available-dosen', [AdminPembimbingController::class, 'availableDosen']);
        Route::post('/pembimbing', [AdminPembimbingController::class, 'store']);
        Route::put('/pembimbing/{pembimbing}', [AdminPembimbingController::class, 'update']);
        Route::delete('/pembimbing/{pembimbing}', [AdminPembimbingController::class, 'destroy']);

        // Master Data
        Route::apiResource('mahasiswa', MasterMahasiswaController::class);
        Route::apiResource('dosen', MasterDosenController::class);

        // Seminar Management
        Route::apiResource('seminar', AdminSeminarController::class);
        Route::post('/seminar/{seminar}/berita-acara', [AdminSeminarController::class, 'createBeritaAcara']);

        // Dokumen Management
        Route::apiResource('dokumen', AdminDokumenController::class);
        Route::get('/dokumen/{dokumen}/download', [AdminDokumenController::class, 'download']);

        // PDF Generation
        Route::get('/pdf/sk-tugas/{skripsi}', [AdminPdfController::class, 'skTugas']);
        Route::get('/pdf/sk-tugas/{skripsi}/preview', [AdminPdfController::class, 'previewSkTugas']);
        Route::get('/pdf/nota-bimbingan/{skripsi}', [AdminPdfController::class, 'notaBimbingan']);
        Route::get('/pdf/berita-acara/{seminar}', [AdminPdfController::class, 'beritaAcaraSeminar']);

        // Bimbingan Management
        Route::get('/bimbingan', [AdminBimbinganController::class, 'index']);
        Route::get('/bimbingan/{skripsi}', [AdminBimbinganController::class, 'show']);

        // Ujian Management
        Route::get('/ujian', [AdminUjianController::class, 'index']);
        Route::get('/ujian/{ujian}', [AdminUjianController::class, 'show']);
        Route::post('/ujian', [AdminUjianController::class, 'store']);
        Route::put('/ujian/{ujian}', [AdminUjianController::class, 'update']);

        // Berita Acara
        Route::get('/berita-acara', [AdminBeritaAcaraController::class, 'index']);

        // Nota Bimbingan
        Route::get('/nota-bimbingan', [AdminNotaBimbinganController::class, 'index']);

        // SK Tugas
        Route::get('/sk-tugas', [AdminSKTugasController::class, 'index']);
        Route::put('/sk-tugas/{skripsi}', [AdminSKTugasController::class, 'update']);

        // SK Yudisium
        Route::get('/sk-yudisium', [AdminSKYudisiumController::class, 'index']);
        Route::post('/sk-yudisium', [AdminSKYudisiumController::class, 'store']);

        // User Management
        Route::get('/users', [AdminUserController::class, 'index']);
        Route::get('/users/{user}', [AdminUserController::class, 'show']);
        Route::post('/users', [AdminUserController::class, 'store']);
        Route::put('/users/{user}', [AdminUserController::class, 'update']);
        Route::delete('/users/{user}', [AdminUserController::class, 'destroy']);
        Route::post('/users/{user}/reset-password', [AdminUserController::class, 'resetPassword']);
    });


// Dosen Routes
use App\Http\Controllers\Api\Dosen\DashboardController as DosenDashboardController;
use App\Http\Controllers\Api\Dosen\BimbinganController as DosenBimbinganController;

Route::prefix('dosen')
    ->middleware(['auth:sanctum', 'role:dosen'])
    ->group(function () {
        Route::get('/dashboard', [DosenDashboardController::class, 'index']);

        // Bimbingan Management
        Route::get('/bimbingan', [DosenBimbinganController::class, 'index']);
        Route::get('/bimbingan/{skripsi}', [DosenBimbinganController::class, 'show']);
        Route::get('/bimbingan/{skripsi}/logs', [DosenBimbinganController::class, 'logs']);
        Route::put('/bimbingan/log/{bimbingan}/status', [DosenBimbinganController::class, 'updateStatus']);
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
        Route::put('/skripsi', [MahasiswaSkripsiController::class, 'update']);
        Route::get('/skripsi/bimbingan', [MahasiswaSkripsiController::class, 'bimbingan']);
        Route::post('/skripsi/bimbingan', [MahasiswaSkripsiController::class, 'addBimbingan']);
    });
