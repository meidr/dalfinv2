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
        Schema::create('berita_acara', function (Blueprint $table) {
            $table->id();
            $table->enum('jenis', ['seminar', 'ujian'])->default('seminar');
            $table->foreignId('seminar_id')->nullable()->constrained('seminar')->onDelete('cascade');
            $table->foreignId('ujian_id')->nullable()->constrained('ujian')->onDelete('cascade');
            $table->string('nomor')->unique();
            $table->date('tanggal');
            $table->enum('hasil', ['lulus', 'lulus_bersyarat', 'tidak_lulus', 'mengulang'])->nullable();
            $table->text('catatan')->nullable();
            $table->string('file_ba')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('berita_acara');
    }
};
