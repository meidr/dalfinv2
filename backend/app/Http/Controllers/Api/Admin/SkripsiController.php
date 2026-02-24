<?php

namespace App\Http\Controllers\Api\Admin;

use App\Http\Controllers\Controller;
use App\Models\Dokumen;
use App\Models\Skripsi;
use App\Models\SkripsiHistory;
use Illuminate\Http\Request;

class SkripsiController extends Controller
{
    /**
     * Display a listing of skripsi
     */
    public function index(Request $request)
    {
        $query = Skripsi::with(['mahasiswa.prodi', 'pembimbing.dosen']);

        // Staff: only see mahasiswa matching their gender
        $user = $request->user();
        if ($user->role === 'staff' && $user->jenis_kelamin) {
            $query->whereHas('mahasiswa', function ($q) use ($user) {
                $q->where('jenis_kelamin', $user->jenis_kelamin);
            });
        }

        // Filters
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        if ($request->filled('prodi_id')) {
            $query->whereHas('mahasiswa', function ($q) use ($request) {
                $q->where('prodi_id', $request->prodi_id);
            });
        }

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('judul', 'like', "%{$search}%")
                    ->orWhereHas('mahasiswa', function ($mhs) use ($search) {
                        $mhs->where('nama', 'like', "%{$search}%")
                            ->orWhere('nim', 'like', "%{$search}%");
                    });
            });
        }

        $perPage = $request->get('per_page', 15);
        // Sorting
        $sortBy = $request->get('sort_by', 'created_at');
        $sortOrder = $request->get('sort_order', 'desc');

        if ($sortBy === 'mahasiswa_nama') {
            $query->join('mahasiswa', 'skripsi.mahasiswa_id', '=', 'mahasiswa.id')
                ->orderBy('mahasiswa.nama', $sortOrder)
                ->select('skripsi.*');
        } else {
            $allowedSorts = ['created_at', 'judul', 'status'];
            if (in_array($sortBy, $allowedSorts)) {
                $query->orderBy($sortBy, $sortOrder);
            } else {
                $query->orderBy('created_at', 'desc');
            }
        }

        $skripsi = $query->paginate($perPage);

        return response()->json([
            'success' => true,
            'data' => $skripsi
        ]);
    }

    /**
     * Store a newly created skripsi
     */
    public function store(Request $request)
    {
        $request->validate([
            'mahasiswa_id' => 'required|exists:mahasiswa,id',
            'judul' => 'required|string|max:500',
            'abstrak' => 'nullable|string',
            'kata_kunci' => 'nullable|string',
            'status' => 'nullable|string',
            'file_skripsi' => 'nullable|file|mimes:pdf,doc,docx|max:10240', // 10MB
        ]);

        // Logic to validate file presence based on status
        $requiredFileStatuses = ['proposal', 'sempro', 'semhas', 'revisi'];
        if (in_array($request->status, $requiredFileStatuses) && !$request->hasFile('file_skripsi')) {
            return response()->json([
                'success' => false,
                'message' => 'File skripsi wajib diupload untuk status ' . $request->status
            ], 422);
        }

        $filePath = null;
        if ($request->hasFile('file_skripsi')) {
            $file = $request->file('file_skripsi');
            $fileName = time() . '_' . $file->getClientOriginalName();
            $filePath = $file->storeAs('skripsi_files', $fileName, 'public');
        }

        $isActive = $request->has('is_active') ? filter_var($request->is_active, FILTER_VALIDATE_BOOLEAN) : true;

        // If this skripsi will be active, deactivate all other skripsi for the same mahasiswa
        if ($isActive) {
            Skripsi::where('mahasiswa_id', $request->mahasiswa_id)
                ->update(['is_active' => false]);
        }

        $skripsi = Skripsi::create([
            'mahasiswa_id' => $request->mahasiswa_id,
            'judul' => $request->judul,
            'abstrak' => $request->abstrak,
            'kata_kunci' => $request->kata_kunci,
            'status' => $request->status ?? 'pengajuan',
            'file_skripsi' => $filePath,
            'tanggal_daftar' => now(),
            'semester_daftar' => $this->getCurrentSemester(),
            'is_active' => $isActive,
        ]);

        $skripsi->logHistory(null, null, 'Pendaftaran skripsi baru', $request->user());

        return response()->json([
            'success' => true,
            'message' => 'Skripsi berhasil ditambahkan',
            'data' => $skripsi->load(['mahasiswa', 'pembimbing.dosen'])
        ], 201);
    }

    /**
     * Display the specified skripsi
     */
    public function show(Skripsi $skripsi)
    {
        $skripsi->load([
            'mahasiswa.prodi',
            'pembimbing.dosen',
            'bimbingan.dosen',
            'seminar.penguji.dosen',
            'ujian.penguji.dosen',
            'dokumen',
            'nilai',
            'skTugas',
            'notaBimbingan',
            'skYudisium',
            'history.updatedBy'
        ]);

        return response()->json([
            'success' => true,
            'data' => $skripsi
        ]);
    }

    /**
     * Update the specified skripsi
     */
    public function update(Request $request, Skripsi $skripsi)
    {
        $request->validate([
            'judul' => 'sometimes|string|max:500',
            'abstrak' => 'nullable|string',
            'kata_kunci' => 'nullable|string',
            'status' => 'sometimes|string',
            'catatan_admin' => 'nullable|string',
            'is_active' => 'sometimes',
            'file_skripsi' => 'nullable|file|mimes:pdf,doc,docx|max:10240',
        ]);

        // Logic to validate file presence based on status
        $requiredFileStatuses = ['proposal', 'sempro', 'semhas', 'revisi'];
        $newStatus = $request->input('status', $skripsi->status);

        if (in_array($newStatus, $requiredFileStatuses)) {
            // Check if file already exists on skripsi OR new file is uploaded OR dokumen of matching type exists
            $hasDokumen = Dokumen::where('skripsi_id', $skripsi->id)
                ->where('jenis', $newStatus)
                ->exists();

            if (!$skripsi->file_skripsi && !$request->hasFile('file_skripsi') && !$hasDokumen) {
                return response()->json([
                    'success' => false,
                    'message' => 'File skripsi wajib diupload untuk status ' . $newStatus
                ], 422);
            }
        }

        $oldTitle = $skripsi->judul;
        $oldStatus = $skripsi->status;

        $fillData = $request->only([
            'judul',
            'abstrak',
            'kata_kunci',
            'status',
            'catatan_admin'
        ]);

        // Handle is_active toggle
        if ($request->has('is_active')) {
            $isActive = filter_var($request->is_active, FILTER_VALIDATE_BOOLEAN);
            $fillData['is_active'] = $isActive;

            // If setting to active, deactivate all other skripsi for the same mahasiswa
            if ($isActive) {
                Skripsi::where('mahasiswa_id', $skripsi->mahasiswa_id)
                    ->where('id', '!=', $skripsi->id)
                    ->update(['is_active' => false]);
            }
        }

        if ($request->hasFile('file_skripsi')) {
            // Delete old file if exists
            if ($skripsi->file_skripsi && \Illuminate\Support\Facades\Storage::disk('public')->exists($skripsi->file_skripsi)) {
                \Illuminate\Support\Facades\Storage::disk('public')->delete($skripsi->file_skripsi);
            }
            $file = $request->file('file_skripsi');
            $fileName = time() . '_' . $file->getClientOriginalName();
            $fillData['file_skripsi'] = $file->storeAs('skripsi_files', $fileName, 'public');
        }

        // Check for sensitive changes (Status or Title)
        $needsVerification = false;
        $judulChanged = $request->has('judul') && $oldTitle !== $request->judul;
        $statusChanged = $oldStatus !== $newStatus;

        if ($judulChanged || $statusChanged) {
            $user = $request->user();
            $isStaff = $user->role === 'staff';

            if ($isStaff) {
                // Staff: needs admin verification
                $needsVerification = true;

                $skripsi->history()->create([
                    'judul_lama' => $oldTitle,
                    'judul_baru' => $request->judul ?? $oldTitle,
                    'status_lama' => $oldStatus,
                    'status_baru' => $newStatus,
                    'alasan' => $request->alasan,
                    'verification_status' => 'pending',
                    'updated_by' => $user->id,
                ]);

                // Remove sensitive fields from direct update
                unset($fillData['judul']);
                unset($fillData['status']);
            } else {
                // Admin/Super Admin: apply directly, log as approved
                $skripsi->history()->create([
                    'judul_lama' => $oldTitle,
                    'judul_baru' => $request->judul ?? $oldTitle,
                    'status_lama' => $oldStatus,
                    'status_baru' => $newStatus,
                    'alasan' => $request->alasan,
                    'verification_status' => 'approved',
                    'updated_by' => $user->id,
                ]);
            }
        }

        $skripsi->fill($fillData);

        // Update progress based on status ONLY if status was actually updated (not pending)
        if (!$needsVerification) {
            $skripsi->progress_percentage = $this->calculateProgress($skripsi->status);
        }

        $skripsi->save();




        $message = $needsVerification
            ? 'Perubahan status/judul berhasil diajukan dan menunggu verifikasi admin.'
            : 'Data skripsi berhasil diperbarui';

        return response()->json([
            'success' => true,
            'message' => $message,
            'data' => $skripsi->load(['mahasiswa', 'pembimbing.dosen'])
        ]);
    }

    /**
     * Remove the specified skripsi
     */
    public function destroy(Skripsi $skripsi)
    {
        // Delete related data (Cascade)
        $skripsi->bimbingan()->delete();
        $skripsi->skTugas()->delete();

        // Delete the Skripsi record permanently
        $skripsi->delete();

        return response()->json([
            'success' => true,
            'message' => 'Skripsi berhasil dihapus permanen beserta data terkait'
        ]);
    }

    /**
     * Get current semester string
     */
    private function getCurrentSemester(): string
    {
        $month = now()->month;
        $year = now()->year;

        if ($month >= 2 && $month <= 7) {
            return "Genap " . ($year - 1) . "/" . $year;
        } else {
            $startYear = $month >= 8 ? $year : $year - 1;
            return "Ganjil " . $startYear . "/" . ($startYear + 1);
        }
    }

    /**
     * Calculate progress percentage based on status
     */
    private function calculateProgress(string $status): int
    {
        $progressMap = [
            'draft' => 0,
            'pengajuan' => 5,
            'disetujui' => 10,
            'ditolak' => 0,
            'proposal' => 15,
            'sempro' => 25,
            'penentuan_dospem' => 30,
            'dospem' => 40,
            'bimbingan' => 50,
            'pengajuan_sidang' => 60,
            'semhas' => 70,
            'ujian' => 80,
            'sidang' => 85,
            'revisi' => 90,
            'lulus' => 100,
        ];

        return $progressMap[$status] ?? 0;
    }
}
