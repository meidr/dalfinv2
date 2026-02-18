<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        // 1. Update seminar jenis ENUM: add 'sidang', migrate data, remove 'ujian'
        DB::statement("ALTER TABLE seminar MODIFY COLUMN jenis ENUM('sempro','semhas','ujian','sidang') NOT NULL");
        DB::table('seminar')->where('jenis', 'ujian')->update(['jenis' => 'sidang']);
        DB::statement("ALTER TABLE seminar MODIFY COLUMN jenis ENUM('sempro','semhas','sidang') NOT NULL");

        // 2. Remove 'ujian' from skripsi status ENUM, migrate data
        DB::table('skripsi')->where('status', 'ujian')->update(['status' => 'sidang']);
        DB::statement("ALTER TABLE skripsi MODIFY COLUMN status ENUM('draft','pengajuan','disetujui','ditolak','proposal','sempro','bimbingan','semhas','sidang','revisi','lulus') DEFAULT 'draft'");
    }

    public function down(): void
    {
        // Re-add 'ujian' to seminar jenis
        DB::statement("ALTER TABLE seminar MODIFY COLUMN jenis ENUM('sempro','semhas','sidang','ujian') NOT NULL");

        // Re-add 'ujian' to skripsi status
        DB::statement("ALTER TABLE skripsi MODIFY COLUMN status ENUM('draft','pengajuan','disetujui','ditolak','proposal','sempro','bimbingan','semhas','ujian','sidang','revisi','lulus') DEFAULT 'draft'");
    }
};
