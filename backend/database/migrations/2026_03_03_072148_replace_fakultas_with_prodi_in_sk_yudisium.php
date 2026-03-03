<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('sk_yudisium', function (Blueprint $table) {
            // Drop fakultas_id column
            $table->dropForeign(['fakultas_id']);
            $table->dropColumn('fakultas_id');

            // Add prodi_id column
            $table->foreignId('prodi_id')->nullable()->after('th_akademik_id')->constrained('prodi')->nullOnDelete();
        });
    }

    public function down(): void
    {
        Schema::table('sk_yudisium', function (Blueprint $table) {
            $table->dropForeign(['prodi_id']);
            $table->dropColumn('prodi_id');

            $table->foreignId('fakultas_id')->nullable()->after('th_akademik_id')->constrained('fakultas')->nullOnDelete();
        });
    }
};
