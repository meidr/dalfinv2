<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        DB::table('skripsi')
            ->whereIn('status', ['pengajuan_sidang', 'pengajuan_sidang_tolak'])
            ->update(['progress_percentage' => 60]);

        DB::table('skripsi')
            ->where('status', 'pengajuan_sidang_acc')
            ->update(['progress_percentage' => 65]);
    }

    public function down(): void
    {
        DB::table('skripsi')
            ->whereIn('status', ['pengajuan_sidang', 'pengajuan_sidang_tolak'])
            ->update(['progress_percentage' => 50]);
    }
};
