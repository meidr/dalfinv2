<?php

namespace Database\Seeders;

use App\Models\User;
use App\Models\Prodi;
use App\Models\Dosen;
use App\Models\Mahasiswa;
use App\Models\Skripsi;
use App\Models\Pembimbing;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // Create Prodi
        $prodi1 = Prodi::create([
            'kode' => 'TI',
            'nama' => 'Teknik Informatika',
            'fakultas' => 'Fakultas Teknik',
            'jenjang' => 'S1',
        ]);

        $prodi2 = Prodi::create([
            'kode' => 'SI',
            'nama' => 'Sistem Informasi',
            'fakultas' => 'Fakultas Teknik',
            'jenjang' => 'S1',
        ]);

        // Create Admin User
        User::create([
            'name' => 'Administrator',
            'email' => 'admin@example.com',
            'password' => Hash::make('password'),
            'role' => 'admin',
            'is_active' => true,
        ]);

        // Create Dosen Users
        $dosenUser1 = User::create([
            'name' => 'Dr. Andi Wijaya, M.Kom',
            'email' => 'andi@example.com',
            'password' => Hash::make('password'),
            'role' => 'dosen',
            'is_active' => true,
        ]);

        $dosen1 = Dosen::create([
            'user_id' => $dosenUser1->id,
            'nip' => '198501012010011001',
            'nama' => 'Andi Wijaya',
            'gelar_depan' => 'Dr.',
            'gelar_belakang' => 'M.Kom',
            'jabatan_fungsional' => 'Lektor',
            'bidang_keahlian' => 'Machine Learning, Data Science',
            'prodi_id' => $prodi1->id,
            'email' => 'andi@example.com',
            'no_hp' => '081234567890',
            'jenis_kelamin' => 'L',
            'kuota_bimbingan' => 10,
        ]);

        $dosenUser2 = User::create([
            'name' => 'Ir. Siti Aminah, M.T.',
            'email' => 'siti@example.com',
            'password' => Hash::make('password'),
            'role' => 'dosen',
            'is_active' => true,
        ]);

        $dosen2 = Dosen::create([
            'user_id' => $dosenUser2->id,
            'nip' => '198805152012012002',
            'nama' => 'Siti Aminah',
            'gelar_depan' => 'Ir.',
            'gelar_belakang' => 'M.T.',
            'jabatan_fungsional' => 'Asisten Ahli',
            'bidang_keahlian' => 'Software Engineering, Web Development',
            'prodi_id' => $prodi1->id,
            'email' => 'siti@example.com',
            'no_hp' => '081234567891',
            'jenis_kelamin' => 'P',
            'kuota_bimbingan' => 8,
        ]);

        // Create Mahasiswa Users
        $mhsUser1 = User::create([
            'name' => 'Budi Santoso',
            'email' => 'budi@example.com',
            'password' => Hash::make('password'),
            'role' => 'mahasiswa',
            'is_active' => true,
        ]);

        $mahasiswa1 = Mahasiswa::create([
            'user_id' => $mhsUser1->id,
            'nim' => '201910234',
            'nama' => 'Budi Santoso',
            'prodi_id' => $prodi1->id,
            'semester' => 8,
            'angkatan' => '2019',
            'jenis_kelamin' => 'L',
            'email' => 'budi@example.com',
            'no_hp' => '081234567892',
        ]);

        $mhsUser2 = User::create([
            'name' => 'Dewi Lestari',
            'email' => 'dewi@example.com',
            'password' => Hash::make('password'),
            'role' => 'mahasiswa',
            'is_active' => true,
        ]);

        $mahasiswa2 = Mahasiswa::create([
            'user_id' => $mhsUser2->id,
            'nim' => '202010123',
            'nama' => 'Dewi Lestari',
            'prodi_id' => $prodi1->id,
            'semester' => 6,
            'angkatan' => '2020',
            'jenis_kelamin' => 'P',
            'email' => 'dewi@example.com',
            'no_hp' => '081234567893',
        ]);

        // Create Skripsi
        $skripsi1 = Skripsi::create([
            'mahasiswa_id' => $mahasiswa1->id,
            'judul' => 'Sistem Pendukung Keputusan Pemilihan Jurusan Menggunakan Metode AHP',
            'abstrak' => 'Penelitian ini mengembangkan sistem pendukung keputusan untuk membantu calon mahasiswa memilih jurusan yang sesuai menggunakan metode Analytical Hierarchy Process (AHP).',
            'kata_kunci' => 'SPK, AHP, Pemilihan Jurusan, Decision Support System',
            'status' => 'bimbingan',
            'tanggal_daftar' => now()->subMonths(3),
            'semester_daftar' => 'Ganjil 2024/2025',
            'progress_percentage' => 50,
        ]);

        // Assign Pembimbing
        Pembimbing::create([
            'skripsi_id' => $skripsi1->id,
            'dosen_id' => $dosen1->id,
            'jenis' => 'pembimbing_1',
            'tanggal_penetapan' => now()->subMonths(2),
        ]);

        Pembimbing::create([
            'skripsi_id' => $skripsi1->id,
            'dosen_id' => $dosen2->id,
            'jenis' => 'pembimbing_2',
            'tanggal_penetapan' => now()->subMonths(2),
        ]);

        // Create another skripsi
        $skripsi2 = Skripsi::create([
            'mahasiswa_id' => $mahasiswa2->id,
            'judul' => 'Implementasi Machine Learning untuk Klasifikasi Penyakit Tanaman',
            'abstrak' => 'Penelitian ini mengembangkan model machine learning untuk mengklasifikasikan penyakit pada tanaman berdasarkan citra daun.',
            'kata_kunci' => 'Machine Learning, Klasifikasi, Tanaman, Image Classification',
            'status' => 'pengajuan',
            'tanggal_daftar' => now()->subWeeks(1),
        ]);

        $this->command->info('Database seeded successfully!');
        $this->command->info('');
        $this->command->info('Sample accounts:');
        $this->command->info('Admin: admin@example.com / password');
        $this->command->info('Dosen: andi@example.com / password (NIP: 198501012010011001)');
        $this->command->info('Mahasiswa: budi@example.com / password (NIM: 201910234)');
    }
}
