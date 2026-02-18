<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('penguji', function (Blueprint $table) {
            $table->decimal('nilai_mt', 5, 2)->nullable()->after('nilai')->comment('Metodologi & Teknik');
            $table->decimal('nilai_ms', 5, 2)->nullable()->after('nilai_mt')->comment('Materi Skripsi');
            $table->decimal('nilai_pm', 5, 2)->nullable()->after('nilai_ms')->comment('Penampilan Mahasiswa');
            $table->decimal('nilai_pi', 5, 2)->nullable()->after('nilai_pm')->comment('Penguasaan Isi');
        });
    }

    public function down(): void
    {
        Schema::table('penguji', function (Blueprint $table) {
            $table->dropColumn(['nilai_mt', 'nilai_ms', 'nilai_pm', 'nilai_pi']);
        });
    }
};
