<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class SuperAdminSeeder extends Seeder
{
    public function run(): void
    {
        User::updateOrCreate(
            ['username' => 'superadmin1'],
            [
                'name' => 'Super Admin 1',
                'email' => 'superadmin1@admin.com',
                'password' => Hash::make('superadmin1'),
                'role' => 'super_admin',
                'is_active' => true,
            ]
        );

        User::updateOrCreate(
            ['username' => 'superadmin2'],
            [
                'name' => 'Super Admin 2',
                'email' => 'superadmin2@admin.com',
                'password' => Hash::make('superadmin2'),
                'role' => 'super_admin',
                'is_active' => true,
            ]
        );

        $this->command->info('✓ 2 super admin berhasil di-seed');
    }
}
