<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('mentor_sempro', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('skripsi_id');
            $table->foreign('skripsi_id')->references('id')->on('skripsi')->onDelete('cascade');
            
            $table->unsignedBigInteger('dosen_id');
            $table->foreign('dosen_id')->references('id')->on('dosen')->onDelete('cascade');
            $table->string('jenis')->default('mentor_1'); // mentor_1, mentor_2
            $table->date('tanggal_penetapan')->nullable();
            $table->boolean('is_active')->default(true);
            $table->timestamps();

            $table->unique(['skripsi_id', 'jenis']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('mentor_sempro');
    }
};
