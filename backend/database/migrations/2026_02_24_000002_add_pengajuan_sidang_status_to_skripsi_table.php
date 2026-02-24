<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        DB::statement("ALTER TABLE skripsi MODIFY COLUMN status ENUM('draft','pengajuan','disetujui','ditolak','proposal','sempro','penentuan_dospem','dospem','bimbingan','pengajuan_sidang','semhas','ujian','sidang','revisi','lulus') DEFAULT 'draft'");
    }

    public function down(): void
    {
        DB::statement("ALTER TABLE skripsi MODIFY COLUMN status ENUM('draft','pengajuan','disetujui','ditolak','proposal','sempro','penentuan_dospem','dospem','bimbingan','semhas','ujian','sidang','revisi','lulus') DEFAULT 'draft'");
    }
};
