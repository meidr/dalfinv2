<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('skripsi', function (Blueprint $table) {
            $table->foreignId('th_akademik_id')->nullable()->after('mahasiswa_id')->constrained('tahuns')->nullOnDelete();
        });
    }

    public function down(): void
    {
        Schema::table('skripsi', function (Blueprint $table) {
            $table->dropForeign(['th_akademik_id']);
            $table->dropColumn('th_akademik_id');
        });
    }
};
