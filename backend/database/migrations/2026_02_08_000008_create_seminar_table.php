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
        Schema::create('seminar', function (Blueprint $table) {
            $table->id();
            $table->foreignId('skripsi_id')->constrained('skripsi')->onDelete('cascade');
            $table->enum('jenis', ['sempro', 'semhas'])->default('sempro');
            $table->date('tanggal')->nullable();
            $table->time('waktu')->nullable();
            $table->string('ruangan')->nullable();
            $table->enum('status', ['terjadwal', 'berlangsung', 'selesai', 'batal'])->default('terjadwal');
            $table->decimal('nilai', 5, 2)->nullable();
            $table->text('catatan')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('seminar');
    }
};
