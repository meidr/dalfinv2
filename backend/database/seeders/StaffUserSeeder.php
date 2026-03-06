<?php

namespace Database\Seeders;

use App\Models\User;
use App\Models\Prodi;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class StaffUserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     * Creates 2 admin users + 2 staff per prodi (1 male, 1 female).
     */
    public function run(): void
    {
        // ========================================
        // ADMIN USERS (2)
        // ========================================
        User::updateOrCreate(
            ['username' => 'admin1'],
            [
                'name' => 'Admin Laki-laki',
                'email' => 'admin1@admin.com',
                'password' => Hash::make('admin1'),
                'role' => 'admin',
                'jenis_kelamin' => 'L',
                'is_active' => true,
            ]
        );

        User::updateOrCreate(
            ['username' => 'admin2'],
            [
                'name' => 'Admin Perempuan',
                'email' => 'admin2@admin.com',
                'password' => Hash::make('admin2'),
                'role' => 'admin',
                'jenis_kelamin' => 'P',
                'is_active' => true,
            ]
        );

        $this->command->info('✓ 2 admin berhasil di-seed');

        // ========================================
        // STAFF USERS (2 per prodi)
        // ========================================
        $prodiList = Prodi::all();
        $staffCount = 0;

        foreach ($prodiList as $prodi) {
            $kode = strtolower(trim($prodi->kode));

            // Staff Laki-laki
            User::updateOrCreate(
                ['username' => "staff_{$kode}_l"],
                [
                    'name' => "Staff {$prodi->nama} (L)",
                    'email' => "staff_{$kode}_l@staff.local",
                    'password' => Hash::make('password'),
                    'role' => 'staff',
                    'jenis_kelamin' => 'L',
                    'is_active' => true,
                ]
            );

            // Staff Perempuan
            User::updateOrCreate(
                ['username' => "staff_{$kode}_p"],
                [
                    'name' => "Staff {$prodi->nama} (P)",
                    'email' => "staff_{$kode}_p@staff.local",
                    'password' => Hash::make('password'),
                    'role' => 'staff',
                    'jenis_kelamin' => 'P',
                    'is_active' => true,
                ]
            );

            $staffCount += 2;
        }

        $this->command->info("✓ {$staffCount} staff berhasil di-seed ({$prodiList->count()} prodi × 2)");
    }
}
