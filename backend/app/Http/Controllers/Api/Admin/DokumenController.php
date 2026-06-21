<?php

namespace App\Http\Controllers\Api\Admin;

use App\Http\Controllers\Controller;
use App\Models\Dokumen;
use App\Models\Seminar;
use App\Models\Skripsi;
use App\Models\SkripsiHistory;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class DokumenController extends Controller
{
    /**
     * List all documents for a skripsi
     */
    public function index(Request $request)
    {
        $query = Dokumen::with(['skripsi.mahasiswa', 'uploader']);

        if ($request->filled('skripsi_id')) {
            $query->where('skripsi_id', $request->skripsi_id);
        }

        if ($request->filled('jenis')) {
            $query->where('jenis', $request->jenis);
        }

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        $perPage = $request->get('per_page', 15);
        $dokumen = $query->orderBy('created_at', 'desc')->paginate($perPage);

        return response()->json([
            'success' => true,
            'data' => $dokumen
        ]);
    }

    /**
     * Upload a new document
     */
    public function store(Request $request)
    {
        $request->validate([
            'skripsi_id' => 'required|exists:skripsi,id',
            'jenis' => 'required|string',
            'file' => 'required|file|mimes:pdf,doc,docx|max:20480',
        ]);

        $skripsi = Skripsi::findOrFail($request->skripsi_id);
        $file = $request->file('file');

        // Check for existing document
        $existingDoc = Dokumen::where('skripsi_id', $skripsi->id)
            ->where('jenis', $request->jenis)
            ->first();

        // Delete old file if exists
        if ($existingDoc && Storage::disk('public')->exists($existingDoc->path)) {
            Storage::disk('public')->delete($existingDoc->path);
        }

        // Generate filename
        $filename = $skripsi->mahasiswa->nim . '_' . $request->jenis . '_v' . time() . '.' . $file->getClientOriginalExtension();
        $path = $file->storeAs('dokumen/' . $skripsi->id, $filename, 'public');

        $dokumen = Dokumen::updateOrCreate(
            [
                'skripsi_id' => $skripsi->id,
                'jenis' => $request->jenis,
            ],
            [
                'nama_file' => $file->getClientOriginalName(),
                'path' => $path,
                'ukuran' => $file->getSize(),
                'versi' => 1,
                'status' => $request->status ?? 'pending',
                'uploaded_by' => $request->user()->id,
            ]
        );

        // Auto-update skripsi status to bimbingan when SK Tugas is approved
        if ($request->jenis === 'sk_tugas' && ($request->status ?? 'pending') === 'approved') {
            Skripsi::where('id', $skripsi->id)
                ->whereIn('status', ['pengajuan', 'disetujui', 'penentuan_dospem', 'dospem'])
                ->update(['status' => 'bimbingan', 'progress_percentage' => 50]);
        }

        return response()->json([
            'success' => true,
            'message' => 'Dokumen berhasil diupload',
            'data' => $dokumen
        ], 201);
    }

    /**
     * Show document detail
     */
    public function show(Dokumen $dokumen)
    {
        $dokumen->load(['skripsi.mahasiswa', 'uploader']);

        return response()->json([
            'success' => true,
            'data' => $dokumen
        ]);
    }

    /**
     * Update document status
     */
    public function update(Request $request, string $id)
    {
        $dokumen = Dokumen::findOrFail($id);
        $request->validate([
            'status' => 'required|in:pending,approved,rejected',
            'catatan' => 'nullable|string',
        ]);

        $dokumen->status = $request->status;
        $dokumen->catatan = $request->catatan;
        $dokumen->save();

        // Auto-update skripsi status to bimbingan when SK Tugas is approved
        if ($dokumen->jenis === 'sk_tugas' && $request->status === 'approved') {
            Skripsi::where('id', $dokumen->skripsi_id)
                ->whereIn('status', ['pengajuan', 'disetujui', 'penentuan_dospem', 'dospem'])
                ->update(['status' => 'bimbingan', 'progress_percentage' => 50]);
        }

        // Auto-update skripsi status to lulus when revision document is approved
        if ($dokumen->jenis === 'revisi' && $request->status === 'approved') {
            $skripsi = Skripsi::with('mahasiswa')->find($dokumen->skripsi_id);

            if ($skripsi && $skripsi->status === 'revisi') {
                if ($skripsi->mahasiswa && $skripsi->mahasiswa->jenis_kelamin === 'P') {
                    // Perempuan: butuh verifikasi admin tambahan, buat history pending
                    $skripsi->history()->create([
                        'judul_lama' => $skripsi->judul,
                        'judul_baru' => $skripsi->judul,
                        'status_lama' => 'revisi',
                        'status_baru' => 'lulus',
                        'alasan' => 'Dokumen revisi disetujui staff, menunggu verifikasi admin',
                        'verification_status' => 'pending',
                        'updated_by' => $request->user()->id,
                    ]);
                } else {
                    // Laki-laki: langsung advance ke lulus
                    $skripsi->update(['status' => 'lulus', 'progress_percentage' => 100]);

                    // Also update sidang seminar hasil from lulus_revisi to lulus
                    Seminar::where('skripsi_id', $skripsi->id)
                        ->where('jenis', 'sidang')
                        ->where('hasil', 'lulus_revisi')
                        ->update(['hasil' => 'lulus']);
                }
            }
        }

        // Auto-update skripsi status to penentuan_dospem when revisi_proposal is approved
        // This is used for lulus_bersyarat sempro flow
        if ($dokumen->jenis === 'revisi_proposal' && $request->status === 'approved') {
            $skripsi = Skripsi::find($dokumen->skripsi_id);

            if ($skripsi && $skripsi->status === 'sempro') {
                $skripsi->update([
                    'status' => 'penentuan_dospem',
                    'progress_percentage' => 30,
                ]);
            }
        }

        return response()->json([
            'success' => true,
            'message' => 'Status dokumen berhasil diperbarui',
            'data' => $dokumen
        ]);
    }

    /**
     * Delete document
     */
    public function destroy(Dokumen $dokumen)
    {
        // Delete file from storage
        if ($dokumen->path && Storage::disk('public')->exists($dokumen->path)) {
            Storage::disk('public')->delete($dokumen->path);
        }

        $dokumen->delete();

        return response()->json([
            'success' => true,
            'message' => 'Dokumen berhasil dihapus'
        ]);
    }

    /**
     * Download document
     */
    public function download(Dokumen $dokumen)
    {
        if (!$dokumen->path || !Storage::disk('public')->exists($dokumen->path)) {
            return response()->json([
                'success' => false,
                'message' => 'File tidak ditemukan'
            ], 404);
        }

        /** @var \Illuminate\Filesystem\FilesystemAdapter $disk */
        $disk = Storage::disk('public');

        return $disk->download($dokumen->path, $dokumen->nama_file);
    }

    /**
     * View/stream document inline (bypasses nginx symlink 403 issues)
     */
    public function view(Dokumen $dokumen)
    {
        if (!$dokumen->path || !Storage::disk('public')->exists($dokumen->path)) {
            abort(404, 'File tidak ditemukan');
        }

        $mimeType = Storage::disk('public')->mimeType($dokumen->path) ?: 'application/octet-stream';

        return Storage::disk('public')->response($dokumen->path, $dokumen->nama_file, [
            'Content-Type' => $mimeType,
        ]);
    }
}
