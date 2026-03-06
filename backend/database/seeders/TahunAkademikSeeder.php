<?php

namespace Database\Seeders;

use App\Models\Tahun;
use Illuminate\Database\Seeder;

class TahunAkademikSeeder extends Seeder
{
    public function run(): void
    {
        $data = [
            ['id' => 5,  'kode' => '20161', 'name' => '2016/2017', 'semester' => 'Ganjil', 'is_active' => false],
            ['id' => 6,  'kode' => '20162', 'name' => '2016/2017', 'semester' => 'Genap',  'is_active' => false],
            ['id' => 7,  'kode' => '20171', 'name' => '2017/2018', 'semester' => 'Ganjil', 'is_active' => false],
            ['id' => 8,  'kode' => '20172', 'name' => '2017/2018', 'semester' => 'Genap',  'is_active' => false],
            ['id' => 9,  'kode' => '20181', 'name' => '2018/2019', 'semester' => 'Ganjil', 'is_active' => false],
            ['id' => 10, 'kode' => '20182', 'name' => '2018/2019', 'semester' => 'Genap',  'is_active' => false],
            ['id' => 11, 'kode' => '20191', 'name' => '2019/2020', 'semester' => 'Ganjil', 'is_active' => false],
            ['id' => 12, 'kode' => '20192', 'name' => '2019/2020', 'semester' => 'Genap',  'is_active' => false],
            ['id' => 13, 'kode' => '20201', 'name' => '2020/2021', 'semester' => 'Ganjil', 'is_active' => false],
            ['id' => 14, 'kode' => '20202', 'name' => '2020/2021', 'semester' => 'Genap',  'is_active' => false],
            ['id' => 15, 'kode' => '20211', 'name' => '2021/2022', 'semester' => 'Ganjil', 'is_active' => false],
            ['id' => 16, 'kode' => '20212', 'name' => '2021/2022', 'semester' => 'Genap',  'is_active' => false],
            ['id' => 17, 'kode' => '20221', 'name' => '2022/2023', 'semester' => 'Ganjil', 'is_active' => false],
            ['id' => 18, 'kode' => '20222', 'name' => '2022/2023', 'semester' => 'Genap',  'is_active' => false],
            ['id' => 19, 'kode' => '20231', 'name' => '2023/2024', 'semester' => 'Ganjil', 'is_active' => false],
            ['id' => 20, 'kode' => '20232', 'name' => '2023/2024', 'semester' => 'Genap',  'is_active' => false],
            ['id' => 21, 'kode' => '20241', 'name' => '2024/2025', 'semester' => 'Ganjil', 'is_active' => false],
            ['id' => 22, 'kode' => '20242', 'name' => '2024/2025', 'semester' => 'Genap',  'is_active' => false],
            ['id' => 24, 'kode' => '20251', 'name' => '2025/2026', 'semester' => 'Ganjil', 'is_active' => false],
            ['id' => 25, 'kode' => '20252', 'name' => '2025/2026', 'semester' => 'Genap',  'is_active' => true],
        ];

        $inserted = 0;
        $skipped = 0;

        foreach ($data as $item) {
            if (Tahun::where('kode', $item['kode'])->exists()) {
                $skipped++;
                continue;
            }

            Tahun::create($item);
            $inserted++;
        }

        // Set semua nonaktif, lalu aktifkan yang terakhir
        Tahun::query()->update(['is_active' => false]);
        Tahun::where('kode', '20252')->update(['is_active' => true]);

        $this->command->info("Tahun Akademik: {$inserted} ditambahkan, {$skipped} sudah ada (dilewati). Aktif: 20252 (2025/2026 Genap).");
    }
}
