<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('skripsi_similarities', function (Blueprint $table) {
            $table->id();
            $table->foreignId('skripsi_id')->constrained('skripsi')->onDelete('cascade');
            $table->foreignId('compared_skripsi_id')->constrained('skripsi')->onDelete('cascade');
            $table->decimal('similarity_score', 5, 2)->default(0);
            $table->string('category', 20)->default('tidak_mirip');
            $table->timestamps();

            $table->unique(['skripsi_id', 'compared_skripsi_id'], 'similarity_pair_unique');
            $table->index('similarity_score');
            $table->index('category');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('skripsi_similarities');
    }
};
