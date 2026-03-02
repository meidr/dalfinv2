<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('panduan', function (Blueprint $table) {
            $table->id();
            $table->enum('type', ['mahasiswa', 'dosen', 'staff']);
            $table->string('nama_file');
            $table->string('file_path');
            $table->unsignedBigInteger('ukuran')->default(0);
            $table->foreignId('uploaded_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamps();

            $table->index('type');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('panduan');
    }
};
