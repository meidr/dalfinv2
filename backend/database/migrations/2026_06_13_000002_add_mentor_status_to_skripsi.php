<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        // Add new status values to skripsi status column
        // Since MySQL enum can't be easily altered, we change column to string type
        // The column is already a string in the current schema based on usage patterns
        // No schema change needed - status is stored as string, not enum
        // Just add the kuota_mentor column to dosen table
        Schema::table('dosen', function (Blueprint $table) {
            $table->integer('kuota_mentor')->nullable()->after('kuota_bimbingan');
        });
    }

    public function down(): void
    {
        Schema::table('dosen', function (Blueprint $table) {
            $table->dropColumn('kuota_mentor');
        });
    }
};
