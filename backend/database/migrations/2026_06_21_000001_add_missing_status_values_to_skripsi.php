<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        // Add missing enum values: penentuan_mentor, mentor, dospem
        DB::statement("ALTER TABLE `skripsi` MODIFY COLUMN `status` ENUM(
            'draft',
            'pengajuan',
            'disetujui',
            'ditolak',
            'proposal',
            'sempro',
            'penentuan_dospem',
            'penentuan_mentor',
            'mentor',
            'dospem',
            'bimbingan',
            'semhas',
            'sidang',
            'revisi',
            'lulus'
        ) NOT NULL DEFAULT 'draft'");
    }

    public function down(): void
    {
        DB::statement("ALTER TABLE `skripsi` MODIFY COLUMN `status` ENUM(
            'draft',
            'pengajuan',
            'disetujui',
            'ditolak',
            'proposal',
            'sempro',
            'penentuan_dospem',
            'bimbingan',
            'semhas',
            'sidang',
            'revisi',
            'lulus'
        ) NOT NULL DEFAULT 'draft'");
    }
};
