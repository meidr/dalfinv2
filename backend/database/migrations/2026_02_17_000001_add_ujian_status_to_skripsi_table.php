<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        DB::statement("ALTER TABLE skripsi MODIFY COLUMN status ENUM('draft','pengajuan','disetujui','ditolak','proposal','sempro','bimbingan','semhas','ujian','sidang','revisi','lulus') DEFAULT 'draft'");
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        DB::statement("ALTER TABLE skripsi MODIFY COLUMN status ENUM('draft','pengajuan','disetujui','ditolak','proposal','sempro','bimbingan','semhas','sidang','revisi','lulus') DEFAULT 'draft'");
    }
};
