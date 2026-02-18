<?php

namespace App\Http\Controllers\Api\Admin;

use App\Http\Controllers\Controller;
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

        $skripsi = Skripsi::create([
            'mahasiswa_id' => $request->mahasiswa_id,
            'judul' => $request->judul,
            'abstrak' => $request->abstrak,
            'kata_kunci' => $request->kata_kunci,
            'status' => $request->status ?? 'pengajuan',
            'file_skripsi' => $filePath,
            'tanggal_daftar' => now(),
            'semester_daftar' => $this->getCurrentSemester(),
            'is_active' => true,
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
            'file_skripsi' => 'nullable|file|mimes:pdf,doc,docx|max:10240',
        ]);

        // Logic to validate file presence based on status
        $requiredFileStatuses = ['proposal', 'sempro', 'semhas', 'revisi'];
        $newStatus = $request->input('status', $skripsi->status);

        if (in_array($newStatus, $requiredFileStatuses)) {
            // Check if file already exists OR new file is uploaded
            if (!$skripsi->file_skripsi && !$request->hasFile('file_skripsi')) {
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
        if ($oldTitle !== $request->judul || $oldStatus !== $newStatus) {
            $needsVerification = true;

            // Create pending history record
            $skripsi->history()->create([
                'judul_lama' => $oldTitle,
                'judul_baru' => $request->judul,
                'status_lama' => $oldStatus,
                'status_baru' => $newStatus,
                'alasan' => $request->alasan, // Ensure 'alasan' is sent from frontend
                'verification_status' => 'pending',
                'updated_by' => $request->user()->id,
            ]);

            // Remove sensitive fields from direct update
            unset($fillData['judul']);
            unset($fillData['status']);
        }

        $skripsi->fill($fillData);

        // Update progress based on status ONLY if status was actually updated (not pending)
        if (!$needsVerification) {
            $skripsi->progress_percentage = $this->calculateProgress($skripsi->status);
        }

        $skripsi->save();

        // Log history if title or status changed (This original log logic is now handled by verification logic above,
        // but we might want to keep it for non-sensitive changes if any?
        // Actually, the requirement status/title change needs verification.
        // If we want to log other things, we might need another mechanism,
        // but for now I will assume this replaces the immediate log)

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
            'bimbingan' => 50,
            'semhas' => 70,
            'ujian' => 80,
            'sidang' => 85,
            'revisi' => 90,
            'lulus' => 100,
        ];

        return $progressMap[$status] ?? 0;
    }
}
