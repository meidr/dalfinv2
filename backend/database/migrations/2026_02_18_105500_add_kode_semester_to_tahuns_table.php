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
        Schema::table('tahuns', function (Blueprint $table) {
            $table->string('kode')->nullable()->after('name');
            $table->string('semester')->nullable()->after('kode');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('tahuns', function (Blueprint $table) {
            $table->dropColumn(['kode', 'semester']);
        });
    }
};
