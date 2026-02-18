<?php

namespace App\Http\Middleware;

use App\Models\ActivityLog;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class LogActivity
{
    /**
     * Route pattern → human-readable description mapping
     */
    private array $routeDescriptions = [
        // Skripsi
        'POST api/admin/skripsi' => 'Menambahkan data skripsi baru',
        'PUT api/admin/skripsi/{id}' => 'Mengubah data skripsi',
        'DELETE api/admin/skripsi/{id}' => 'Menghapus data skripsi',

        // Pembimbing
        'POST api/admin/pembimbing' => 'Menambahkan pembimbing baru',
        'PUT api/admin/pembimbing/{id}' => 'Mengubah data pembimbing',
        'DELETE api/admin/pembimbing/{id}' => 'Menghapus pembimbing',

        // Master Data
        'POST api/admin/mahasiswa' => 'Menambahkan data mahasiswa baru',
        'PUT api/admin/mahasiswa/{id}' => 'Mengubah data mahasiswa',
        'DELETE api/admin/mahasiswa/{id}' => 'Menghapus data mahasiswa',
        'POST api/admin/dosen' => 'Menambahkan data dosen baru',
        'PUT api/admin/dosen/{id}' => 'Mengubah data dosen',
        'DELETE api/admin/dosen/{id}' => 'Menghapus data dosen',

        // Seminar
        'POST api/admin/seminar' => 'Menambahkan jadwal seminar baru',
        'PUT api/admin/seminar/{id}' => 'Mengubah data seminar',
        'DELETE api/admin/seminar/{id}' => 'Menghapus data seminar',
        'POST api/admin/seminar/{id}/berita-acara' => 'Membuat berita acara seminar',
        'POST api/admin/seminar/{id}/penguji' => 'Menambahkan penguji ke seminar',
        'PUT api/admin/seminar/{id}/penguji/{id2}' => 'Mengubah data penguji seminar',
        'DELETE api/admin/seminar/{id}/penguji/{id2}' => 'Menghapus penguji dari seminar',

        // Dokumen
        'POST api/admin/dokumen' => 'Mengunggah dokumen baru',
        'PUT api/admin/dokumen/{id}' => 'Mengubah data dokumen',
        'DELETE api/admin/dokumen/{id}' => 'Menghapus dokumen',

        // PDF
        'POST api/admin/pdf/sk-tugas/{id}' => 'Membuat/memperbarui SK Tugas',
        'POST api/admin/pdf/sk-penguji/{id}' => 'Membuat/memperbarui SK Penguji',

        // Ujian
        'POST api/admin/ujian' => 'Menambahkan jadwal ujian baru',
        'PUT api/admin/ujian/{id}' => 'Mengubah data ujian',

        // Berita Acara
        'POST api/admin/berita-acara/{id}/generate' => 'Membuat berita acara',

        // SK Tugas
        'PUT api/admin/sk-tugas/{id}' => 'Mengubah data SK Tugas',

        // SK Yudisium
        'POST api/admin/sk-yudisium' => 'Menambahkan SK Yudisium baru',

        // User Management
        'POST api/admin/users' => 'Menambahkan user baru',
        'PUT api/admin/users/{id}' => 'Mengubah data user',
        'POST api/admin/users/{id}/toggle-status' => 'Mengubah status aktif user',
        'POST api/admin/users/{id}/reset-password' => 'Mereset password user',

        // Configuration
        'POST api/admin/configuration/sk-tugas-signer' => 'Mengubah konfigurasi penandatangan SK Tugas',

        // Super Admin
        'POST api/super-admin/impersonate/{id}' => 'Melakukan impersonasi user',
        'POST api/super-admin/stop-impersonate' => 'Menghentikan impersonasi',
        'POST api/super-admin/force-logout-all' => 'Melakukan force logout semua user',
        'POST api/super-admin/toggle-system-lock' => 'Mengubah status kunci sistem',
    ];

    /**
     * Log write requests (POST, PUT, DELETE) automatically
     */
    public function handle(Request $request, Closure $next): Response
    {
        $response = $next($request);

        // Only log write operations
        if (in_array($request->method(), ['POST', 'PUT', 'PATCH', 'DELETE']) && $request->user()) {
            $action = match ($request->method()) {
                'POST' => 'create',
                'PUT', 'PATCH' => 'update',
                'DELETE' => 'delete',
                default => 'unknown',
            };

            $routePath = $request->path();
            $method = strtoupper($request->method());
            $description = "{$method} /{$routePath}";

            // Generate human-readable detail
            $detail = $this->generateDetail($request, $method, $routePath);

            ActivityLog::create([
                'user_id' => $request->user()->id,
                'action' => $action,
                'description' => $description,
                'detail' => $detail,
                'ip_address' => $request->ip(),
                'user_agent' => $request->userAgent(),
            ]);
        }

        return $response;
    }

    /**
     * Generate human-readable detail from route path and request data
     */
    private function generateDetail(Request $request, string $method, string $routePath): string
    {
        $userName = $request->user()->name ?? 'Unknown';

        // Try to match route pattern
        $matchedDescription = $this->matchRoute($method, $routePath);

        if ($matchedDescription) {
            $detail = "{$userName} — {$matchedDescription}";
        } else {
            $detail = "{$userName} melakukan aksi pada sistem";
        }

        // Add context based on request data for specific routes
        $extra = $this->extractContext($request, $routePath);
        if ($extra) {
            $detail .= ". {$extra}";
        }

        return $detail;
    }

    /**
     * Match a route path to its human-readable description
     */
    private function matchRoute(string $method, string $routePath): ?string
    {
        // Normalize: replace numeric segments with {id}
        $normalizedPath = preg_replace('/\/\d+/', '/{id}', $routePath);
        $key = "{$method} {$normalizedPath}";

        // Direct match
        if (isset($this->routeDescriptions[$key])) {
            return $this->routeDescriptions[$key];
        }

        // Try replacing second {id} for nested routes like seminar/{id}/penguji/{id}
        $secondKey = preg_replace('/\{id\}(.*)\{id\}/', '{id}$1{id2}', $key);
        if (isset($this->routeDescriptions[$secondKey])) {
            return $this->routeDescriptions[$secondKey];
        }

        return null;
    }

    /**
     * Extract meaningful context from request body
     */
    private function extractContext(Request $request, string $routePath): ?string
    {
        $parts = [];

        // Skripsi context
        if (str_contains($routePath, 'skripsi')) {
            if ($request->filled('judul')) {
                $parts[] = 'Judul: "' . $request->input('judul') . '"';
            }
            if ($request->filled('status')) {
                $parts[] = 'Status: ' . $this->translateStatus($request->input('status'));
            }
        }

        // User context
        if (str_contains($routePath, 'users')) {
            if ($request->filled('name')) {
                $parts[] = 'Nama: ' . $request->input('name');
            }
            if ($request->filled('role')) {
                $parts[] = 'Role: ' . $this->translateRole($request->input('role'));
            }
            if (str_contains($routePath, 'toggle-status')) {
                $parts[] = 'Toggle status aktif/blokir';
            }
            if (str_contains($routePath, 'reset-password')) {
                $parts[] = 'Password direset ke default';
            }
        }

        // Seminar context
        if (str_contains($routePath, 'seminar') && !str_contains($routePath, 'penguji')) {
            if ($request->filled('jenis')) {
                $parts[] = 'Jenis: ' . ucfirst($request->input('jenis'));
            }
            if ($request->filled('tanggal')) {
                $parts[] = 'Tanggal: ' . $request->input('tanggal');
            }
        }

        // Ujian context
        if (str_contains($routePath, 'ujian')) {
            if ($request->filled('jenis')) {
                $parts[] = 'Jenis: ' . ucfirst($request->input('jenis'));
            }
            if ($request->filled('tanggal')) {
                $parts[] = 'Tanggal: ' . $request->input('tanggal');
            }
        }

        // Mahasiswa context
        if (str_contains($routePath, 'mahasiswa')) {
            if ($request->filled('nama')) {
                $parts[] = 'Nama: ' . $request->input('nama');
            }
            if ($request->filled('nim')) {
                $parts[] = 'NIM: ' . $request->input('nim');
            }
        }

        // Dosen context
        if (str_contains($routePath, 'dosen') && !str_contains($routePath, 'available-dosen')) {
            if ($request->filled('nama')) {
                $parts[] = 'Nama: ' . $request->input('nama');
            }
            if ($request->filled('nip')) {
                $parts[] = 'NIP: ' . $request->input('nip');
            }
        }

        // Pembimbing context
        if (str_contains($routePath, 'pembimbing')) {
            if ($request->filled('peran')) {
                $parts[] = 'Peran: ' . ucfirst($request->input('peran'));
            }
        }

        return !empty($parts) ? implode(', ', $parts) : null;
    }

    /**
     * Translate skripsi status to Indonesian
     */
    private function translateStatus(string $status): string
    {
        return match ($status) {
            'pengajuan' => 'Pengajuan Judul',
            'pengajuan_proposal' => 'Pengajuan Proposal',
            'seminar_proposal' => 'Seminar Proposal',
            'penelitian' => 'Penelitian',
            'seminar_hasil' => 'Seminar Hasil',
            'ujian_skripsi' => 'Ujian Skripsi',
            'revisi' => 'Revisi',
            'selesai' => 'Selesai',
            'ditolak' => 'Ditolak',
            default => ucfirst(str_replace('_', ' ', $status)),
        };
    }

    /**
     * Translate role to Indonesian
     */
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
}
