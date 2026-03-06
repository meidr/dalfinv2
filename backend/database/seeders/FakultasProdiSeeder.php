<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class FakultasProdiSeeder extends Seeder
{
    /**
     * Seed fakultas and prodi data.
     */
    public function run(): void
    {
        // ========================================
        // FAKULTAS
        // ========================================
        $fakultasData = [
            ['id' => 1, 'kode' => 'FT', 'nama_fakultas' => 'Fakultas Tarbiyah'],
            ['id' => 2, 'kode' => 'FS', 'nama_fakultas' => 'Fakultas Syariah'],
            ['id' => 3, 'kode' => 'FA', 'nama_fakultas' => 'Fakultas Adab'],
            ['id' => 4, 'kode' => 'FD', 'nama_fakultas' => 'Fakultas Dakwah'],
        ];

        foreach ($fakultasData as $f) {
            DB::table('fakultas')->insert([
                'id' => $f['id'],
                'kode' => $f['kode'],
                'nama_fakultas' => $f['nama_fakultas'],
                'dekan_id' => null,
                'wakil_dekan_id' => null,
                'is_active' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }

        $this->command->info('✓ ' . count($fakultasData) . ' fakultas berhasil di-seed');

        // ========================================
        // PRODI
        // ========================================
        $prodiData = [
            ['id' => 1,  'kode' => '88888',  'nama' => 'Pondok - Jamiah',                              'jenjang' => '---', 'fakultas_id' => null],
            ['id' => 3,  'kode' => '60202',  'nama' => 'Ekonomi Syariah',                               'jenjang' => 'S1',  'fakultas_id' => 2],
            ['id' => 4,  'kode' => '70232',  'nama' => 'Bimbingan Dan Konseling Islam',                 'jenjang' => 'S1',  'fakultas_id' => 4],
            ['id' => 5,  'kode' => '70233',  'nama' => 'Komunikasi dan Penyiaran Islam',                'jenjang' => 'S1',  'fakultas_id' => 4],
            ['id' => 6,  'kode' => '74230',  'nama' => 'Hukum Keluarga Islam (Ahwal Al Syakhshiyah)',   'jenjang' => 'S1',  'fakultas_id' => 2],
            ['id' => 7,  'kode' => '80230',  'nama' => 'Sejarah Peradaban Islam',                       'jenjang' => 'S1',  'fakultas_id' => 3],
            ['id' => 8,  'kode' => '86208',  'nama' => 'Pendidikan Agama Islam',                        'jenjang' => 'S1',  'fakultas_id' => 1],
            ['id' => 9,  'kode' => '86231',  'nama' => 'Manajemen Pendidikan Islam',                    'jenjang' => 'S1',  'fakultas_id' => 1],
            ['id' => 10, 'kode' => '88204',  'nama' => 'Pendidikan Bahasa Arab',                        'jenjang' => 'S1',  'fakultas_id' => 1],
            ['id' => 11, 'kode' => '88104',  'nama' => 'Pendidikan Bahasa Arab S2',                     'jenjang' => 'S2',  'fakultas_id' => null],
            ['id' => 12, 'kode' => '86131',  'nama' => 'Manajemen Pendidikan Islam S2',                 'jenjang' => 'S2',  'fakultas_id' => null],
            ['id' => 13, 'kode' => '86008',  'nama' => 'Pendidikan Agama Islam S3',                     'jenjang' => 'S3',  'fakultas_id' => null],
            ['id' => 14, 'kode' => '99999',  'nama' => 'Pondok - Yayasan',                              'jenjang' => '---', 'fakultas_id' => null],
            ['id' => 16, 'kode' => '70234',  'nama' => 'Manajemen Haji dan Umroh',                      'jenjang' => 'S1',  'fakultas_id' => 4],
            ['id' => 17, 'kode' => '80231',  'nama' => 'Ilmu AlQuran dan Tafsir',                       'jenjang' => 'S1',  'fakultas_id' => 3],
            ['id' => 18, 'kode' => '80232',  'nama' => 'Sastra Arab',                                   'jenjang' => 'S1',  'fakultas_id' => 3],
            ['id' => 19, 'kode' => '88889',  'nama' => 'Magister Hukum Keluarga Islam',                 'jenjang' => 'S2',  'fakultas_id' => null],
        ];

        foreach ($prodiData as $p) {
            DB::table('prodi')->insert([
                'id' => $p['id'],
                'kode' => $p['kode'],
                'nama' => $p['nama'],
                'fakultas_id' => $p['fakultas_id'],
                'jenjang' => $p['jenjang'],
                'is_active' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }

        $this->command->info('✓ ' . count($prodiData) . ' prodi berhasil di-seed');
    }
}
