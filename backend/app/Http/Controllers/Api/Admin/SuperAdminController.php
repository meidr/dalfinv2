<?php

namespace App\Http\Controllers\Api\Admin;

use App\Http\Controllers\Controller;
use App\Models\ActivityLog;
use App\Models\SystemSetting;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Session;
use Carbon\Carbon;

class SuperAdminController extends Controller
{
    /**
     * Get paginated activity logs
     */
    public function activityLogs(Request $request)
    {
        $query = ActivityLog::with('user:id,name,email,role')
            ->orderBy('created_at', 'desc');

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('description', 'like', "%{$search}%")
                    ->orWhere('action', 'like', "%{$search}%")
                    ->orWhereHas('user', function ($uq) use ($search) {
                        $uq->where('name', 'like', "%{$search}%")
                            ->orWhere('email', 'like', "%{$search}%");
                    });
            });
        }

        if ($request->filled('action')) {
            $query->where('action', $request->action);
        }

        if ($request->filled('user_id')) {
            $query->where('user_id', $request->user_id);
        }

        if ($request->filled('date_from')) {
            $from = Carbon::parse($request->date_from, 'Asia/Jakarta')->startOfDay()->utc();
            $query->where('created_at', '>=', $from);
        }

        if ($request->filled('date_to')) {
            $to = Carbon::parse($request->date_to, 'Asia/Jakarta')->endOfDay()->utc();
            $query->where('created_at', '<=', $to);
        }

        $perPage = max(5, min((int) $request->get('per_page', 20), 100));
        $logs = $query->paginate($perPage);

        return response()->json([
            'success' => true,
            'data' => $logs,
        ]);
    }

    /**
     * Export activity logs to Excel/CSV
     */
    public function exportActivityLogs(Request $request)
    {
        $query = ActivityLog::with('user:id,name,email,role')
            ->orderBy('created_at', 'desc');

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('description', 'like', "%{$search}%")
                    ->orWhere('detail', 'like', "%{$search}%")
                    ->orWhereHas('user', function ($uq) use ($search) {
                        $uq->where('name', 'like', "%{$search}%");
                    });
            });
        }

        if ($request->filled('action')) {
            $query->where('action', $request->action);
        }

        if ($request->filled('date_from')) {
            $from = Carbon::parse($request->date_from, 'Asia/Jakarta')->startOfDay()->utc();
            $query->where('created_at', '>=', $from);
        }

        if ($request->filled('date_to')) {
            $to = Carbon::parse($request->date_to, 'Asia/Jakarta')->endOfDay()->utc();
            $query->where('created_at', '<=', $to);
        }

        $data = $query->get();

        $dateLabel = '';
        if ($request->filled('date_from') && $request->filled('date_to')) {
            $dateLabel = "_{$request->date_from}_sd_{$request->date_to}";
        } elseif ($request->filled('date_from')) {
            $dateLabel = "_dari_{$request->date_from}";
        } elseif ($request->filled('date_to')) {
            $dateLabel = "_sampai_{$request->date_to}";
        }

        $filename = "Log_Aktivitas{$dateLabel}.csv";

        $headers = [
            'Content-Type' => 'text/csv; charset=UTF-8',
            'Content-Disposition' => "attachment; filename=\"{$filename}\"",
        ];

        $callback = function () use ($data) {
            $file = fopen('php://output', 'w');
            // BOM for Excel UTF-8 compatibility
            fprintf($file, chr(0xEF) . chr(0xBB) . chr(0xBF));

            // Header row
            fputcsv($file, [
                'No',
                'Nama User',
                'Email',
                'Role',
                'Aksi',
                'Deskripsi',
                'Detail',
                'Alamat IP',
                'Waktu',
            ]);

            $no = 1;
            foreach ($data as $log) {
                $actionLabel = match ($log->action) {
                    'login' => 'Login',
                    'create' => 'Tambah Data',
                    'update' => 'Ubah Data',
                    'delete' => 'Hapus Data',
                    'impersonate' => 'Impersonasi',
                    'stop_impersonate' => 'Stop Impersonasi',
                    'system_lock' => 'Kunci Sistem',
                    'force_logout' => 'Force Logout',
                    default => ucfirst(str_replace('_', ' ', $log->action)),
                };

                $roleLabel = match ($log->user?->role) {
                    'super_admin' => 'Super Admin',
                    'admin' => 'Admin',
                    'dosen' => 'Dosen',
                    'mahasiswa' => 'Mahasiswa',
                    'staff' => 'Staff',
                    default => $log->user?->role ?? '-',
                };

                fputcsv($file, [
                    $no++,
                    $log->user?->name ?? 'System',
                    $log->user?->email ?? '-',
                    $roleLabel,
                    $actionLabel,
                    $log->description ?? '-',
                    $log->detail ?? '-',
                    $log->ip_address ?? '-',
                    $log->created_at?->format('d/m/Y H:i:s') ?? '-',
                ]);
            }

            fclose($file);
        };

        return response()->stream($callback, 200, $headers);
    }

    /**
     * Impersonate a user — create a token for them
     */
    public function impersonate(Request $request, $userId)
    {
        $targetUser = User::findOrFail($userId);

        if ($targetUser->role === 'super_admin') {
            return response()->json([
                'success' => false,
                'message' => 'Tidak dapat impersonate sesama super admin.',
            ], 400);
        }

        // Log the impersonation
        ActivityLog::log('impersonate', "Impersonate sebagai {$targetUser->name} ({$targetUser->email})", $targetUser);

        // Create a new Sanctum token for the target user
        $token = $targetUser->createToken('impersonation-token')->plainTextToken;

        // Load profile based on role
        if ($targetUser->role === 'mahasiswa') {
            $targetUser->load('mahasiswa.prodi');
        } elseif ($targetUser->role === 'dosen') {
            $targetUser->load('dosen.prodi');
        }

        return response()->json([
            'success' => true,
            'message' => "Berhasil login sebagai {$targetUser->name}",
            'data' => [
                'user' => $targetUser,
                'token' => $token,
                'impersonating' => true,
            ],
        ]);
    }

    /**
     * Stop impersonating — frontend handles token swap back
     */
    public function stopImpersonate(Request $request)
    {
        // Delete the impersonation token
        $request->user()->currentAccessToken()->delete();

        ActivityLog::log('stop_impersonate', "Selesai impersonate");

        return response()->json([
            'success' => true,
            'message' => 'Berhasil kembali ke akun super admin.',
        ]);
    }

    /**
     * Force logout all users (delete all Sanctum tokens except current)
     */
    public function forceLogoutAll(Request $request)
    {
        $currentToken = $request->user()->currentAccessToken();
        $currentTokenId = $currentToken instanceof \Laravel\Sanctum\TransientToken ? null : $currentToken->id;

        // Delete all tokens except the current super admin's token
        $query = DB::table('personal_access_tokens');
        if ($currentTokenId) {
            $query->where('id', '!=', $currentTokenId);
        }
        $query->delete();

        ActivityLog::log('force_logout', 'Force logout semua user');

        return response()->json([
            'success' => true,
            'message' => 'Semua user berhasil di-logout.',
        ]);
    }

    /**
     * Toggle system lock
     */
    public function toggleSystemLock(Request $request)
    {
        $request->validate([
            'message' => 'nullable|string|max:500',
        ]);

        $isLocked = SystemSetting::get('system_locked') === 'true';

        if ($isLocked) {
            // Unlock
            SystemSetting::set('system_locked', 'false');
            SystemSetting::set('lock_message', null);
            SystemSetting::set('locked_by', null);
            ActivityLog::log('system_unlock', 'Sistem dibuka kembali');
        } else {
            // Lock
            SystemSetting::set('system_locked', 'true');
            SystemSetting::set('lock_message', $request->message ?? 'Sistem sedang dalam pemeliharaan.');
            SystemSetting::set('locked_by', $request->user()->id);
            ActivityLog::log('system_lock', 'Sistem dikunci: ' . ($request->message ?? 'Pemeliharaan'));
        }

        return response()->json([
            'success' => true,
            'message' => $isLocked ? 'Sistem berhasil dibuka.' : 'Sistem berhasil dikunci.',
            'data' => [
                'is_locked' => !$isLocked,
            ],
        ]);
    }

    /**
     * Get system status
     */
    public function systemStatus()
    {
        return response()->json([
            'success' => true,
            'data' => [
                'is_locked' => SystemSetting::get('system_locked') === 'true',
                'lock_message' => SystemSetting::get('lock_message'),
                'locked_by' => SystemSetting::get('locked_by'),
                'total_users' => User::count(),
                'active_sessions' => DB::table('sessions')->count(),
                'semhas_enabled' => SystemSetting::get('semhas_enabled', 'true') === 'true',
            ],
        ]);
    }

    /**
     * Get soft-deleted records for restore
     */
    public function trashedRecords(Request $request)
    {
        $type = $request->get('type', 'all');
        $data = [];

        // Only show models that use SoftDeletes
        // For now, return an empty placeholder since we need to check which models use soft deletes
        return response()->json([
            'success' => true,
            'data' => $data,
            'message' => 'Fitur restore data tersedia untuk model yang menggunakan soft delete.',
        ]);
    }

    /**
     * Get all users for impersonation list
     */
    public function userList(Request $request)
    {
        $query = User::where('role', '!=', 'super_admin')
            ->select('id', 'name', 'email', 'role', 'jenis_kelamin', 'is_active', 'created_at');

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

        $perPage = max(5, min((int) $request->get('per_page', 15), 100));
        $users = $query->orderBy('name')->paginate($perPage);

        return response()->json([
            'success' => true,
            'data' => $users,
        ]);
    }

    /**
     * Get module settings
     */
    public function getModuleSettings()
    {
        return response()->json([
            'success' => true,
            'data' => [
                'semhas_enabled' => SystemSetting::get('semhas_enabled', 'true') === 'true',
            ],
        ]);
    }

    /**
     * Toggle seminar hasil module
     */
    public function toggleSemhasModule()
    {
        $isEnabled = SystemSetting::get('semhas_enabled', 'true') === 'true';

        SystemSetting::set('semhas_enabled', $isEnabled ? 'false' : 'true');

        $newState = !$isEnabled;
        ActivityLog::log(
            'update',
            'Modul Seminar Hasil ' . ($newState ? 'diaktifkan' : 'dinonaktifkan')
        );

        return response()->json([
            'success' => true,
            'message' => 'Modul Seminar Hasil berhasil ' . ($newState ? 'diaktifkan' : 'dinonaktifkan'),
            'data' => [
                'semhas_enabled' => $newState,
            ],
        ]);
    }
}
