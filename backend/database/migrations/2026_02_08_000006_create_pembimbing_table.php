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
        Schema::create('pembimbing', function (Blueprint $table) {
            $table->id();
            $table->foreignId('skripsi_id')->constrained('skripsi')->onDelete('cascade');
            $table->foreignId('dosen_id')->constrained('dosen')->onDelete('cascade');
            $table->enum('jenis', ['pembimbing_1', 'pembimbing_2'])->default('pembimbing_1');
            $table->date('tanggal_penetapan')->nullable();
            $table->boolean('is_active')->default(true);
            $table->timestamps();

            // Unique constraint: one dosen can only be one type of pembimbing for one skripsi
            $table->unique(['skripsi_id', 'jenis']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('pembimbing');
    }
};
