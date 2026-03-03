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
        Schema::table('sk_yudisium', function (Blueprint $table) {
            $table->string('nomor_sk_batch')->nullable()->after('nomor_sk');
            $table->foreignId('th_akademik_id')->nullable()->after('nomor_sk_batch')
                ->constrained('tahuns')->nullOnDelete();
            $table->index('nomor_sk_batch');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('sk_yudisium', function (Blueprint $table) {
            $table->dropForeign(['th_akademik_id']);
            $table->dropIndex(['nomor_sk_batch']);
            $table->dropColumn(['nomor_sk_batch', 'th_akademik_id']);
        });
    }
};
