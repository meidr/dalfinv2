<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('document_tokens', function (Blueprint $table) {
            $table->id();
            $table->string('token', 64)->unique()->index();
            $table->string('document_type'); // sk_tugas, berita_acara, sk_penguji, nota_bimbingan, sk_yudisium
            $table->unsignedBigInteger('document_id')->nullable();
            $table->string('nomor_surat')->nullable();
            $table->string('nama_penandatangan');
            $table->string('jabatan_penandatangan');
            $table->string('nama_berkas');
            $table->json('metadata')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('document_tokens');
    }
};
