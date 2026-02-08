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
        Schema::create('dokumen', function (Blueprint $table) {
            $table->id();
            $table->foreignId('skripsi_id')->constrained('skripsi')->onDelete('cascade');
            $table->enum('jenis', [
                'proposal',
                'bab1',
                'bab2',
                'bab3',
                'bab4',
                'bab5',
                'full_draft',
                'final',
                'revisi',
                'surat_pernyataan',
                'lembar_pengesahan',
                'lainnya'
            ])->default('lainnya');
            $table->string('nama_file');
            $table->string('path');
            $table->integer('ukuran')->nullable();
            $table->integer('versi')->default(1);
            $table->enum('status', ['pending', 'approved', 'rejected'])->default('pending');
            $table->text('catatan')->nullable();
            $table->foreignId('uploaded_by')->nullable()->constrained('users')->onDelete('set null');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('dokumen');
    }
};
