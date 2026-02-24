<?php

use Illuminate\Database\Migrations\Migration;
use App\Models\Configuration;

return new class extends Migration
{
    public function up(): void
    {
        Configuration::updateOrCreate(
            ['key' => 'syarat_bimbingan_ujian'],
            ['value' => ['pembimbing_1' => 8, 'pembimbing_2' => 4]]
        );
    }

    public function down(): void
    {
        Configuration::where('key', 'syarat_bimbingan_ujian')->delete();
    }
};
