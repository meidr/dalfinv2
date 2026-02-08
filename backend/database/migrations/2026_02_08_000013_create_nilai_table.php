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
        Schema::create('nilai', function (Blueprint $table) {
            $table->id();
            $table->foreignId('skripsi_id')->constrained('skripsi')->onDelete('cascade');
            $table->enum('jenis', ['sempro', 'semhas', 'sidang', 'bimbingan', 'final'])->default('bimbingan');
            $table->decimal('nilai', 5, 2);
            $table->string('huruf_mutu')->nullable();
            $table->text('keterangan')->nullable();
            $table->foreignId('penilai_id')->nullable()->constrained('dosen')->onDelete('set null');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('nilai');
    }
};
