<?php

namespace App\Http\Controllers\Api\Admin;

use App\Http\Controllers\Controller;
use App\Models\Dokumen;
use App\Models\Skripsi;
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

        // Generate filename
        $filename = $skripsi->mahasiswa->nim . '_' . $request->jenis . '_v' . time() . '.' . $file->getClientOriginalExtension();
        $path = $file->storeAs('dokumen/' . $skripsi->id, $filename, 'public');

        // Get version number
        $lastVersion = Dokumen::where('skripsi_id', $skripsi->id)
            ->where('jenis', $request->jenis)
            ->max('versi') ?? 0;

        $dokumen = Dokumen::create([
            'skripsi_id' => $skripsi->id,
            'jenis' => $request->jenis,
            'nama_file' => $file->getClientOriginalName(),
            'path' => $path,
            'ukuran' => $file->getSize(),
            'versi' => $lastVersion + 1,
            'status' => 'pending',
            'uploaded_by' => $request->user()->id,
        ]);

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
    public function update(Request $request, Dokumen $dokumen)
    {
        $request->validate([
            'status' => 'required|in:pending,approved,rejected',
            'catatan' => 'nullable|string',
        ]);

        $dokumen->status = $request->status;
        $dokumen->catatan = $request->catatan;
        $dokumen->save();

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

        return Storage::disk('public')->download($dokumen->path, $dokumen->nama_file);
    }
}
