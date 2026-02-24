<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('perbaikan_proposal', function (Blueprint $table) {
            $table->id();
            $table->foreignId('seminar_id')->constrained('seminar')->onDelete('cascade');
            $table->integer('no')->default(1);
            $table->string('topik');
            $table->string('halaman')->nullable();
            $table->text('uraian')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('perbaikan_proposal');
    }
};
