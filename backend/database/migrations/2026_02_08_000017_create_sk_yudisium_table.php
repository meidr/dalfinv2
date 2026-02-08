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
        Schema::create('sk_yudisium', function (Blueprint $table) {
            $table->id();
            $table->foreignId('skripsi_id')->constrained('skripsi')->onDelete('cascade');
            $table->string('nomor_sk')->unique();
            $table->date('tanggal_terbit');
            $table->date('tanggal_yudisium')->nullable();
            $table->string('predikat')->nullable();
            $table->decimal('ipk_akhir', 4, 2)->nullable();
            $table->string('file_sk')->nullable();
            $table->text('keterangan')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('sk_yudisium');
    }
};
