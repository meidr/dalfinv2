<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
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
            'pengajuan_sidang',
            'pengajuan_sidang_acc',
            'pengajuan_sidang_tolak',
            'semhas',
            'sidang',
            'revisi',
            'lulus'
        ) NOT NULL DEFAULT 'draft'");
    }

    public function down(): void
    {
        // Fallback to previous
    }
};
