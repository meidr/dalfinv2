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
        // Change 'jenis' column from ENUM to VARCHAR(255) to allow dynamic types like 'sk_tugas', 'sk_yudisium'
        DB::statement("ALTER TABLE dokumen MODIFY COLUMN jenis VARCHAR(255) NOT NULL DEFAULT 'lainnya'");
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Attempt to revert back to ENUM, but this might fail if data contains values not in the list
        // So we'll just leave it as VARCHAR or you can manually restore if needed.
        // DB::statement("ALTER TABLE dokumen MODIFY COLUMN jenis ENUM('proposal','bab1','bab2','bab3','bab4','bab5','full_draft','final','revisi','surat_pernyataan','lembar_pengesahan','lainnya') NOT NULL DEFAULT 'lainnya'");
    }
};
