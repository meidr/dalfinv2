<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\MasterJabatan;

class MasterJabatanSeeder extends Seeder
{
    public function run(): void
    {
        $jabatans = [
            ['kode' => 'REKTOR', 'nama' => 'Rektor', 'level' => 'kampus'],
            ['kode' => 'WAREK', 'nama' => 'Wakil Rektor', 'level' => 'kampus'],
            ['kode' => 'DEKAN', 'nama' => 'Dekan', 'level' => 'fakultas'],
            ['kode' => 'KAPRODI', 'nama' => 'Ketua Program Studi', 'level' => 'prodi'],
        ];

        foreach ($jabatans as $j) {
            MasterJabatan::updateOrCreate(
                ['kode' => $j['kode']],
                $j
            );
        }
    }
}
