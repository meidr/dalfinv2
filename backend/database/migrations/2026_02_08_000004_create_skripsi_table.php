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
        Schema::create('skripsi', function (Blueprint $table) {
            $table->id();
            $table->foreignId('mahasiswa_id')->constrained('mahasiswa')->onDelete('cascade');
            $table->string('judul');
            $table->text('abstrak')->nullable();
            $table->string('kata_kunci')->nullable();
            $table->enum('status', [
                'draft',
                'pengajuan',
                'disetujui',
                'ditolak',
                'proposal',
                'sempro',
                'bimbingan',
                'semhas',
                'sidang',
                'revisi',
                'lulus'
            ])->default('draft');
            $table->date('tanggal_daftar')->nullable();
            $table->string('semester_daftar')->nullable();
            $table->integer('progress_percentage')->default(0);
            $table->text('catatan_admin')->nullable();
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('skripsi');
    }
};
