<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // Modify jenis enum to include 'ujian'
        DB::statement("ALTER TABLE seminar MODIFY COLUMN jenis ENUM('sempro', 'semhas', 'ujian') DEFAULT 'sempro'");

        // Modify status enum to include 'pending'
        DB::statement("ALTER TABLE seminar MODIFY COLUMN status ENUM('terjadwal', 'berlangsung', 'selesai', 'batal', 'pending') DEFAULT 'terjadwal'");

        // Add 'hasil' column
        Schema::table('seminar', function (Blueprint $table) {
            $table->string('hasil')->nullable()->after('nilai');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('seminar', function (Blueprint $table) {
            $table->dropColumn('hasil');
        });

        DB::statement("ALTER TABLE seminar MODIFY COLUMN jenis ENUM('sempro', 'semhas') DEFAULT 'sempro'");
        DB::statement("ALTER TABLE seminar MODIFY COLUMN status ENUM('terjadwal', 'berlangsung', 'selesai', 'batal') DEFAULT 'terjadwal'");
    }
};
