<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DummySkripsiSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $juduls = [
            'Sistem Pakar Diagnosa Penyakit Jantung',
            'Aplikasi Sistem Pakar Penyakit Jantung dengan Metode Forward Chaining',
            'Rancang Bangun E-Commerce Berbasis Web',
            'E-Commerce Website Development using Laravel'
        ];

        $tahun = \App\Models\Tahun::first();
        $tahunId = $tahun ? $tahun->id : null;
        $prodi = \App\Models\Prodi::first();
        $prodiId = $prodi ? $prodi->id : 1;

        foreach ($juduls as $k => $v) {
            $user = \App\Models\User::firstOrCreate(
                ['username' => 'mhs' . $k],
                [
                    'name' => 'Mahasiswa ' . $k,
                    'email' => 'mhs'.$k.'@mhs.com',
                    'password' => bcrypt('password'),
                    'role' => 'mahasiswa',
                    'prodi_id' => $prodiId
                ]
            );

            $mhs = \App\Models\Mahasiswa::firstOrCreate(
                ['user_id' => $user->id],
                [
                    'nama' => 'Mahasiswa ' . $k,
                    'nim' => '1000' . $k,
                    'prodi_id' => $prodiId,
                    'jenis_kelamin' => 'L',
                    'status' => 'aktif',
                    'tahun_id' => $tahunId
                ]
            );

            \App\Models\Skripsi::create([
                'mahasiswa_id' => $mhs->id,
                'judul' => $v,
                'status' => 'pengajuan',
                'th_akademik_id' => $tahunId
            ]);
        }
    }
}
