<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        $this->call([
            FakultasProdiSeeder::class,
            TahunAkademikSeeder::class,
            SuperAdminSeeder::class,
            StaffUserSeeder::class,
            MasterJabatanSeeder::class,
        ]);

        $this->command->info('');
        $this->command->info('========================================');
        $this->command->info('  Database seeded successfully!');
        $this->command->info('========================================');
        $this->command->info('');
        $this->command->info('Akun default:');
        $this->command->info('  Super Admin  : superadmin1 / superadmin1');
        $this->command->info('  Super Admin  : superadmin2 / superadmin2');
        $this->command->info('  Admin (L)    : admin1 / admin1');
        $this->command->info('  Admin (P)    : admin2 / admin2');
        $this->command->info('  Staff        : staff_{kode_prodi}_l / password');
        $this->command->info('  Staff        : staff_{kode_prodi}_p / password');
        $this->command->info('');
    }
}
