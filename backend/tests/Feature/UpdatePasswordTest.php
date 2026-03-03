<?php

namespace Tests\Feature;

use Tests\TestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;
use App\Models\User;
use App\Models\Mahasiswa;
use App\Models\Dosen;
use App\Models\Prodi;
use App\Models\Fakultas;
use App\Models\Tahun;
use Illuminate\Support\Facades\Hash;

class UpdatePasswordTest extends TestCase
{
    use RefreshDatabase;

    protected Prodi $prodi;
    protected Tahun $tahun;

    protected function setUp(): void
    {
        parent::setUp();

        $fakultas = Fakultas::create([
            'kode' => 'FT',
            'nama_fakultas' => 'Fakultas Teknik',
            'is_active' => true,
        ]);

        $this->prodi = Prodi::create([
            'kode' => 'TI',
            'nama' => 'Teknik Informatika',
            'fakultas_id' => $fakultas->id,
            'jenjang' => 'S1',
            'is_active' => true,
        ]);

        $this->tahun = Tahun::create([
            'name' => '2025/2026',
            'kode' => '20252',
            'semester' => 'Genap',
            'is_active' => true,
        ]);
    }

    /**
     * Helper: create a user with a given role and optional profile.
     */
    private function createUserWithRole(string $role, string $email): User
    {
        $user = User::create([
            'name' => ucfirst($role) . ' Test',
            'email' => $email,
            'password' => 'oldpassword123',
            'role' => $role,
            'is_active' => true,
        ]);

        // Create profile for roles that need it
        if ($role === 'mahasiswa') {
            Mahasiswa::create([
                'user_id' => $user->id,
                'nim' => '12345678',
                'nama' => 'Mahasiswa Test',
                'prodi_id' => $this->prodi->id,
                'tahun_id' => $this->tahun->id,
                'semester' => 8,
                'is_active' => true,
                'status' => 'aktif',
            ]);
        } elseif ($role === 'dosen') {
            Dosen::create([
                'user_id' => $user->id,
                'nip' => '198801012020',
                'nama' => 'Dosen Test',
                'prodi_id' => $this->prodi->id,
                'is_active' => true,
                'kuota_bimbingan' => 10,
            ]);
        }

        return $user;
    }

    // ====================================================
    //  ADMIN: Update Password
    // ====================================================

    public function test_admin_can_change_password_successfully(): void
    {
        $user = $this->createUserWithRole('admin', 'admin@test.com');

        $response = $this->actingAs($user, 'sanctum')
            ->putJson('/api/auth/password', [
                'current_password' => 'oldpassword123',
                'password' => 'newpassword456',
                'password_confirmation' => 'newpassword456',
            ]);

        $response->assertStatus(200)
            ->assertJsonPath('success', true)
            ->assertJsonPath('message', 'Password berhasil diubah');

        // Verify new password works
        $user->refresh();
        $this->assertTrue(Hash::check('newpassword456', $user->password));
        $this->assertFalse(Hash::check('oldpassword123', $user->password));
    }

    public function test_admin_cannot_change_password_with_wrong_current(): void
    {
        $user = $this->createUserWithRole('admin', 'admin@test.com');

        $response = $this->actingAs($user, 'sanctum')
            ->putJson('/api/auth/password', [
                'current_password' => 'wrongpassword',
                'password' => 'newpassword456',
                'password_confirmation' => 'newpassword456',
            ]);

        $response->assertStatus(422)
            ->assertJsonValidationErrors(['current_password']);

        // Password should remain unchanged
        $user->refresh();
        $this->assertTrue(Hash::check('oldpassword123', $user->password));
    }

    public function test_admin_cannot_change_password_without_confirmation(): void
    {
        $user = $this->createUserWithRole('admin', 'admin@test.com');

        $response = $this->actingAs($user, 'sanctum')
            ->putJson('/api/auth/password', [
                'current_password' => 'oldpassword123',
                'password' => 'newpassword456',
                // missing password_confirmation
            ]);

        $response->assertStatus(422)
            ->assertJsonValidationErrors(['password']);
    }

    public function test_admin_cannot_change_password_with_mismatched_confirmation(): void
    {
        $user = $this->createUserWithRole('admin', 'admin@test.com');

        $response = $this->actingAs($user, 'sanctum')
            ->putJson('/api/auth/password', [
                'current_password' => 'oldpassword123',
                'password' => 'newpassword456',
                'password_confirmation' => 'different789',
            ]);

        $response->assertStatus(422)
            ->assertJsonValidationErrors(['password']);
    }

    public function test_admin_cannot_change_password_too_short(): void
    {
        $user = $this->createUserWithRole('admin', 'admin@test.com');

        $response = $this->actingAs($user, 'sanctum')
            ->putJson('/api/auth/password', [
                'current_password' => 'oldpassword123',
                'password' => 'short',
                'password_confirmation' => 'short',
            ]);

        $response->assertStatus(422)
            ->assertJsonValidationErrors(['password']);
    }

    // ====================================================
    //  STAFF: Update Password
    // ====================================================

    public function test_staff_can_change_password_successfully(): void
    {
        $user = $this->createUserWithRole('staff', 'staff@test.com');

        $response = $this->actingAs($user, 'sanctum')
            ->putJson('/api/auth/password', [
                'current_password' => 'oldpassword123',
                'password' => 'staffnewpass123',
                'password_confirmation' => 'staffnewpass123',
            ]);

        $response->assertStatus(200)
            ->assertJsonPath('success', true)
            ->assertJsonPath('message', 'Password berhasil diubah');

        $user->refresh();
        $this->assertTrue(Hash::check('staffnewpass123', $user->password));
    }

    public function test_staff_cannot_change_password_with_wrong_current(): void
    {
        $user = $this->createUserWithRole('staff', 'staff@test.com');

        $response = $this->actingAs($user, 'sanctum')
            ->putJson('/api/auth/password', [
                'current_password' => 'wrongpassword',
                'password' => 'staffnewpass123',
                'password_confirmation' => 'staffnewpass123',
            ]);

        $response->assertStatus(422)
            ->assertJsonValidationErrors(['current_password']);
    }

    // ====================================================
    //  DOSEN: Update Password
    // ====================================================

    public function test_dosen_can_change_password_successfully(): void
    {
        $user = $this->createUserWithRole('dosen', 'dosen@test.com');

        $response = $this->actingAs($user, 'sanctum')
            ->putJson('/api/auth/password', [
                'current_password' => 'oldpassword123',
                'password' => 'dosennewpass123',
                'password_confirmation' => 'dosennewpass123',
            ]);

        $response->assertStatus(200)
            ->assertJsonPath('success', true)
            ->assertJsonPath('message', 'Password berhasil diubah');

        $user->refresh();
        $this->assertTrue(Hash::check('dosennewpass123', $user->password));
    }

    public function test_dosen_cannot_change_password_with_wrong_current(): void
    {
        $user = $this->createUserWithRole('dosen', 'dosen@test.com');

        $response = $this->actingAs($user, 'sanctum')
            ->putJson('/api/auth/password', [
                'current_password' => 'wrongpassword',
                'password' => 'dosennewpass123',
                'password_confirmation' => 'dosennewpass123',
            ]);

        $response->assertStatus(422)
            ->assertJsonValidationErrors(['current_password']);
    }

    // ====================================================
    //  MAHASISWA: Update Password
    // ====================================================

    public function test_mahasiswa_can_change_password_successfully(): void
    {
        $user = $this->createUserWithRole('mahasiswa', 'mhs@test.com');

        $response = $this->actingAs($user, 'sanctum')
            ->putJson('/api/auth/password', [
                'current_password' => 'oldpassword123',
                'password' => 'mhsnewpass12345',
                'password_confirmation' => 'mhsnewpass12345',
            ]);

        $response->assertStatus(200)
            ->assertJsonPath('success', true)
            ->assertJsonPath('message', 'Password berhasil diubah');

        $user->refresh();
        $this->assertTrue(Hash::check('mhsnewpass12345', $user->password));
    }

    public function test_mahasiswa_cannot_change_password_with_wrong_current(): void
    {
        $user = $this->createUserWithRole('mahasiswa', 'mhs@test.com');

        $response = $this->actingAs($user, 'sanctum')
            ->putJson('/api/auth/password', [
                'current_password' => 'wrongpassword',
                'password' => 'mhsnewpass12345',
                'password_confirmation' => 'mhsnewpass12345',
            ]);

        $response->assertStatus(422)
            ->assertJsonValidationErrors(['current_password']);
    }

    // ====================================================
    //  UNAUTHENTICATED: Cannot change password
    // ====================================================

    public function test_unauthenticated_user_cannot_change_password(): void
    {
        $response = $this->putJson('/api/auth/password', [
            'current_password' => 'oldpassword123',
            'password' => 'newpassword456',
            'password_confirmation' => 'newpassword456',
        ]);

        $response->assertStatus(401);
    }
}
