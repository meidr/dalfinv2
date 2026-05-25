<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('nomor_surat_templates', function (Blueprint $table) {
            $table->id();
            $table->string('key')->unique();
            $table->string('nama');
            $table->enum('level', ['fakultas', 'prodi'])->default('prodi');
            $table->string('template');
            $table->unsignedTinyInteger('digit_urut')->default(3);
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });

        $now = now();
        DB::table('nomor_surat_templates')->insert([
            [
                'key' => 'sk_penguji_sempro',
                'nama' => 'SK Penguji Seminar Proposal',
                'level' => 'prodi',
                'template' => 'SK-PENGUJI-SEMPRO/{nomor_urut}/{prodi_alias}/{bulan}/{tahun}',
                'digit_urut' => 3,
                'is_active' => true,
                'created_at' => $now,
                'updated_at' => $now,
            ],
            [
                'key' => 'ba_sempro',
                'nama' => 'Berita Acara Seminar Proposal',
                'level' => 'prodi',
                'template' => 'BA-SEMPRO/{nomor_urut}/{prodi_alias}/{bulan}/{tahun}',
                'digit_urut' => 3,
                'is_active' => true,
                'created_at' => $now,
                'updated_at' => $now,
            ],
            [
                'key' => 'sk_tugas',
                'nama' => 'SK Tugas Pembimbing',
                'level' => 'prodi',
                'template' => 'SK-TUGAS/{nomor_urut}/{prodi_alias}/{bulan}/{tahun}',
                'digit_urut' => 3,
                'is_active' => true,
                'created_at' => $now,
                'updated_at' => $now,
            ],
            [
                'key' => 'nota_bimbingan',
                'nama' => 'Nota Bimbingan Skripsi',
                'level' => 'prodi',
                'template' => 'NB/{nomor_urut}/{prodi_alias}/{bulan}/{tahun}',
                'digit_urut' => 3,
                'is_active' => true,
                'created_at' => $now,
                'updated_at' => $now,
            ],
            [
                'key' => 'sk_penguji_semhas',
                'nama' => 'SK Penguji Seminar Hasil',
                'level' => 'prodi',
                'template' => 'SK-PENGUJI-SEMHAS/{nomor_urut}/{prodi_alias}/{bulan}/{tahun}',
                'digit_urut' => 3,
                'is_active' => true,
                'created_at' => $now,
                'updated_at' => $now,
            ],
            [
                'key' => 'ba_semhas',
                'nama' => 'Berita Acara Seminar Hasil',
                'level' => 'prodi',
                'template' => 'BA-SEMHAS/{nomor_urut}/{prodi_alias}/{bulan}/{tahun}',
                'digit_urut' => 3,
                'is_active' => true,
                'created_at' => $now,
                'updated_at' => $now,
            ],
            [
                'key' => 'sk_penguji_sidang',
                'nama' => 'SK Penguji Sidang Skripsi',
                'level' => 'fakultas',
                'template' => 'SK-PENGUJI-SIDANG/{nomor_urut}/{Fakultas_kode}/{bulan}/{tahun}',
                'digit_urut' => 3,
                'is_active' => true,
                'created_at' => $now,
                'updated_at' => $now,
            ],
            [
                'key' => 'ba_sidang',
                'nama' => 'Berita Acara Sidang Skripsi',
                'level' => 'fakultas',
                'template' => 'BA-SIDANG/{nomor_urut}/{Fakultas_kode}/{bulan}/{tahun}',
                'digit_urut' => 3,
                'is_active' => true,
                'created_at' => $now,
                'updated_at' => $now,
            ],
            [
                'key' => 'sk_yudisium',
                'nama' => 'SK Yudisium',
                'level' => 'fakultas',
                'template' => 'SK-YUDISIUM/{nomor_urut}/{Fakultas_kode}/{bulan}/{tahun}',
                'digit_urut' => 3,
                'is_active' => true,
                'created_at' => $now,
                'updated_at' => $now,
            ],
        ]);
    }

    public function down(): void
    {
        Schema::dropIfExists('nomor_surat_templates');
    }
};
