<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('tanda_tangan', function (Blueprint $table) {
            $table->id();
            $table->foreignId('dosen_id')->constrained('dosen')->cascadeOnDelete();
            $table->string('ttd'); // file path to the signature image
            $table->timestamps();

            $table->unique('dosen_id'); // one signature per dosen
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('tanda_tangan');
    }
};
