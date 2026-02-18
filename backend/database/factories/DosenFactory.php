<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use App\Models\User;
use App\Models\Prodi;
/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Dosen>
 */
class DosenFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'user_id' => User::factory(),
            'nip' => fake()->numerify('19##############'),
            'nama' => fake()->name(),
            'gelar_depan' => 'Dr.',
            'gelar_belakang' => 'M.Kom',
            'jabatan_fungsional' => 'Lektor',
            'bidang_keahlian' => 'Machine Learning, Data Science',
            'prodi_id' => fake()->randomElement([1, 2]),
            'email' => fake()->unique()->safeEmail(),
            'no_hp' => fake()->phoneNumber(),
            'jenis_kelamin' => fake()->randomElement(['L', 'P']),
            'kuota_bimbingan' => 10,
        ];
    }
}
