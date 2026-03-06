<?php

use App\Models\Dosen;
use App\Models\Mahasiswa;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
});

Route::get('/test', function () {
    return dd(\App\Services\DosenService::all());
    // return dd(\App\Services\MahasiswaService::mahasiswaSkripsi(null, 1, 'arif'));
});
Route::get('/test/2', function () {
    return dd(Dosen::first());
});
