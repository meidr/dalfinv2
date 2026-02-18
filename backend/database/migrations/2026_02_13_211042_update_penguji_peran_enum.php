<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        // Update penguji table
        DB::statement("ALTER TABLE `penguji` MODIFY COLUMN `peran` ENUM('ketua', 'sekretaris', 'anggota', 'penguji_1', 'penguji_2') DEFAULT 'penguji_1'");

        // Update penguji_ujian table if exists
        if (\Schema::hasTable('penguji_ujian')) {
            DB::statement("ALTER TABLE `penguji_ujian` MODIFY COLUMN `peran` ENUM('ketua', 'sekretaris', 'anggota', 'penguji_1', 'penguji_2') DEFAULT 'penguji_1'");
        }
    }

    public function down(): void
    {
        DB::statement("ALTER TABLE `penguji` MODIFY COLUMN `peran` ENUM('ketua', 'sekretaris', 'anggota') DEFAULT 'anggota'");

        if (\Schema::hasTable('penguji_ujian')) {
            DB::statement("ALTER TABLE `penguji_ujian` MODIFY COLUMN `peran` ENUM('ketua', 'sekretaris', 'anggota') DEFAULT 'anggota'");
        }
    }
};
