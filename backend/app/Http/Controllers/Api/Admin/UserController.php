<?php

namespace App\Http\Controllers\Api\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;

class UserController extends Controller
{
    public function index(Request $request)
    {
        $query = User::query();

        // If the requester is NOT super_admin, hide super_admin users
        if ($request->user()->role !== 'super_admin') {
            $query->where('role', '!=', 'super_admin');
        }

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                    ->orWhere('email', 'like', "%{$search}%");
            });
        }

        if ($request->filled('role')) {
            $query->where('role', $request->role);
        }

        if ($request->has('is_active')) {
            $query->where('is_active', $request->boolean('is_active'));
        }

        $users = $query->orderBy('created_at', 'desc')->paginate(10);

        return response()->json([
            'success' => true,
            'data' => $users
        ]);
    }

    public function show($id)
    {
        $user = User::findOrFail($id);

        return response()->json([
            'success' => true,
            'data' => $user
        ]);
    }

    /**
     * Create a new user (super_admin only)
     * Super admin can create admin and super_admin accounts
     */
    public function store(Request $request)
    {
        // Only super_admin can create users
        if ($request->user()->role !== 'super_admin') {
            return response()->json([
                'success' => false,
                'message' => 'Hanya super admin yang dapat menambah user'
            ], 403);
        }

        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email',
            'password' => 'required|string|min:6',
            'role' => 'required|string|in:admin,super_admin',
        ]);

        $validated['password'] = Hash::make($validated['password']);
        $validated['is_active'] = true;

        $user = User::create($validated);

        return response()->json([
            'success' => true,
            'message' => 'User berhasil ditambahkan',
            'data' => $user
        ], 201);
    }

    /**
     * Update user
     * Admin: can only change role to admin/staff
     * Super admin: can change role to admin/super_admin/staff
     */
    public function update(Request $request, $id)
    {
        $user = User::findOrFail($id);

        // Prevent self-modification
        if ($user->id === auth()->id()) {
            return response()->json([
                'success' => false,
                'message' => 'Tidak dapat mengubah akun sendiri'
            ], 400);
        }

        // Admin cannot edit super_admin users
        if ($request->user()->role !== 'super_admin' && $user->role === 'super_admin') {
            return response()->json([
                'success' => false,
                'message' => 'Admin tidak dapat mengedit akun super admin'
            ], 403);
        }

        // Determine allowed roles based on requester's role
        $allowedRoles = $request->user()->role === 'super_admin'
            ? 'in:admin,super_admin,staff'
            : 'in:admin,staff';

        $validated = $request->validate([
            'name' => 'sometimes|string|max:255',
            'email' => ['sometimes', 'email', Rule::unique('users')->ignore($user->id)],
            'role' => "sometimes|string|{$allowedRoles}",
            'is_active' => 'sometimes|boolean',
            'password' => 'sometimes|nullable|string|min:6',
            'phone' => 'sometimes|nullable|string|max:20',
        ]);

        // Hash password if provided
        if (!empty($validated['password'])) {
            $validated['password'] = Hash::make($validated['password']);
        } else {
            unset($validated['password']);
        }

        $user->update($validated);

        return response()->json([
            'success' => true,
            'message' => 'User berhasil diperbarui',
            'data' => $user
        ]);
    }

    /**
     * Toggle user active/blocked status
     */
    public function toggleStatus(Request $request, $id)
    {
        $user = User::findOrFail($id);

        // Prevent self-blocking
        if ($user->id === auth()->id()) {
            return response()->json([
                'success' => false,
                'message' => 'Tidak dapat memblokir akun sendiri'
            ], 400);
        }

        // Admin cannot toggle super_admin
        if ($request->user()->role !== 'super_admin' && $user->role === 'super_admin') {
            return response()->json([
                'success' => false,
                'message' => 'Admin tidak dapat memblokir super admin'
            ], 403);
        }

        $user->is_active = !$user->is_active;
        $user->save();

        return response()->json([
            'success' => true,
            'message' => $user->is_active ? 'User berhasil diaktifkan' : 'User berhasil diblokir',
            'data' => $user
        ]);
    }

    /**
     * Reset user password to default
     */
    public function resetPassword($id)
    {
        $user = User::findOrFail($id);

        $user->update([
            'password' => Hash::make('password')
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Password berhasil direset ke "password"'
        ]);
    }
}
