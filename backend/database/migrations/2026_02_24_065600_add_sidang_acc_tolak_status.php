<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Add pengajuan_sidang_acc and pengajuan_sidang_tolak to status ENUM
        DB::statement("ALTER TABLE skripsi MODIFY COLUMN status ENUM('draft','pengajuan','disetujui','ditolak','proposal','sempro','penentuan_dospem','dospem','bimbingan','pengajuan_sidang','pengajuan_sidang_acc','pengajuan_sidang_tolak','semhas','ujian','sidang','revisi','lulus') DEFAULT 'draft'");

        // Add column for rejection reason
        Schema::table('skripsi', function (Blueprint $table) {
            $table->text('alasan_tolak_sidang')->nullable()->after('status');
        });
    }

    public function down(): void
    {
        // Revert any acc/tolak rows back to pengajuan_sidang before shrinking ENUM
        DB::table('skripsi')
            ->whereIn('status', ['pengajuan_sidang_acc', 'pengajuan_sidang_tolak'])
            ->update(['status' => 'pengajuan_sidang']);

        DB::statement("ALTER TABLE skripsi MODIFY COLUMN status ENUM('draft','pengajuan','disetujui','ditolak','proposal','sempro','penentuan_dospem','dospem','bimbingan','pengajuan_sidang','semhas','ujian','sidang','revisi','lulus') DEFAULT 'draft'");

        Schema::table('skripsi', function (Blueprint $table) {
            $table->dropColumn('alasan_tolak_sidang');
        });
    }
};
