<?php

namespace App\Http\Controllers\Api\Auth;

use App\Http\Controllers\Controller;
use App\Models\ActivityLog;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\ValidationException;

class AuthController extends Controller
{
    /**
     * Login user and create token
     */
    public function login(Request $request)
    {
        $request->validate([
            'username' => 'required|string',
            'password' => 'required|string',
        ]);

        // Attempt to authenticate via username
        if (Auth::attempt(['username' => $request->username, 'password' => $request->password], $request->boolean('remember'))) {
            $request->session()->regenerate();

            $user = Auth::user();
            User::where('id', $user->id)->update(['last_login_at' => now()]);
            ActivityLog::create([
                'user_id' => $user->id,
                'action' => 'login',
                'description' => "Login sebagai {$user->role}: {$user->name} ({$user->username})",
                'detail' => "{$user->name} berhasil login ke sistem sebagai " . $this->translateRole($user->role),
                'ip_address' => $request->ip(),
                'user_agent' => $request->userAgent(),
            ]);

            // Check if user is active
            if (!$user->is_active) {
                Auth::guard('web')->logout();
                $request->session()->invalidate();
                $request->session()->regenerateToken();

                throw ValidationException::withMessages([
                    'username' => ['Akun Anda tidak aktif. Silakan hubungi administrator.'],
                ]);
            }

            // Load profile based on role
            $user->load($user->role === 'mahasiswa' ? ['mahasiswa.prodi', 'mahasiswa.tahun'] : ($user->role === 'dosen' ? 'dosen.prodi' : []));

            return response()->json([
                'success' => true,
                'message' => 'Login berhasil',
                'data' => [
                    'user' => $user,
                ]
            ]);
        }

        throw ValidationException::withMessages([
            'username' => ['Username atau password salah.'],
        ]);
    }

    private function translateRole(string $role): string
    {
        return match ($role) {
            'super_admin' => 'Super Admin',
            'admin' => 'Admin',
            'staff' => 'Staff',
            'dosen' => 'Dosen',
            'mahasiswa' => 'Mahasiswa',
            default => ucfirst($role),
        };
    }

    /**
     * Logout user (revoke token)
     */
    public function logout(Request $request)
    {
        Auth::guard('web')->logout();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return response()->json([
            'success' => true,
            'message' => 'Logout berhasil'
        ]);
    }

    /**
     * Get authenticated user
     */
    public function user(Request $request)
    {
        $user = $request->user();
        $user->load($user->role === 'mahasiswa' ? ['mahasiswa.prodi', 'mahasiswa.tahun'] : ($user->role === 'dosen' ? 'dosen.prodi' : []));

        return response()->json([
            'success' => true,
            'data' => $user
        ]);
    }

    /**
     * Update profile
     */
    public function updateProfile(Request $request)
    {
        $user = $request->user();

        $request->validate([
            'name' => 'sometimes|string|max:255',
            'phone' => 'sometimes|nullable|string|max:20',
            'address' => 'sometimes|nullable|string',
            'avatar' => 'sometimes|nullable|image|max:2048',
        ]);

        if ($request->hasFile('avatar')) {
            $path = $request->file('avatar')->store('avatars', 'public');
            $user->avatar = $path;
        }

        $user->fill($request->only(['name', 'phone', 'address']));
        $user->save();

        return response()->json([
            'success' => true,
            'message' => 'Profil berhasil diperbarui',
            'data' => $user
        ]);
    }

    /**
     * Change password
     */
    public function changePassword(Request $request)
    {
        $request->validate([
            'current_password' => 'required|string',
            'password' => 'required|string|min:8|confirmed',
        ]);

        $user = $request->user();

        if (!Hash::check($request->current_password, $user->password)) {
            throw ValidationException::withMessages([
                'current_password' => ['Password saat ini tidak valid.'],
            ]);
        }

        $user->password = Hash::make($request->password);
        $user->save();

        return response()->json([
            'success' => true,
            'message' => 'Password berhasil diubah'
        ]);
    }
}
