<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // 1. Drop jabatan_id FK from dosen
        Schema::table('dosen', function (Blueprint $table) {
            $table->dropForeign(['jabatan_id']);
            $table->dropColumn('jabatan_id');
        });

        // 2. Drop old jabatans table
        Schema::dropIfExists('jabatans');

        // 3. Create master_jabatan
        Schema::create('master_jabatan', function (Blueprint $table) {
            $table->id();
            $table->string('kode', 30)->unique();
            $table->string('nama', 100);
            $table->enum('level', ['kampus', 'fakultas', 'prodi']);
            $table->timestamps();
        });

        // 4. Create periode_jabatan
        Schema::create('periode_jabatan', function (Blueprint $table) {
            $table->id();
            $table->string('nama', 50);
            $table->date('tgl_mulai');
            $table->date('tgl_selesai');
            $table->boolean('is_active')->default(false);
            $table->timestamps();
        });

        // 5. Create jabatan_pejabat
        Schema::create('jabatan_pejabat', function (Blueprint $table) {
            $table->id();
            $table->foreignId('periode_id')->constrained('periode_jabatan')->cascadeOnDelete();
            $table->foreignId('jabatan_id')->constrained('master_jabatan')->cascadeOnDelete();
            $table->foreignId('dosen_id')->constrained('dosen')->cascadeOnDelete();
            $table->foreignId('prodi_id')->nullable()->constrained('prodi')->nullOnDelete();
            $table->foreignId('fakultas_id')->nullable()->constrained('fakultas')->nullOnDelete();
            $table->date('tgl_mulai');
            $table->date('tgl_selesai')->nullable();
            $table->boolean('is_plt')->default(false);
            $table->string('keterangan', 255)->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('jabatan_pejabat');
        Schema::dropIfExists('periode_jabatan');
        Schema::dropIfExists('master_jabatan');

        // Recreate old table
        Schema::create('jabatans', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->timestamps();
        });

        Schema::table('dosen', function (Blueprint $table) {
            $table->foreignId('jabatan_id')->nullable()->constrained('jabatans')->nullOnDelete()->after('prodi_id');
        });
    }
};
