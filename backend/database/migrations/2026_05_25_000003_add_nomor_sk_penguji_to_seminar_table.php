<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('seminar', function (Blueprint $table) {
            $table->string('nomor_sk_penguji')->nullable()->unique()->after('jenis');
        });
    }

    public function down(): void
    {
        Schema::table('seminar', function (Blueprint $table) {
            $table->dropUnique(['nomor_sk_penguji']);
            $table->dropColumn('nomor_sk_penguji');
        });
    }
};
