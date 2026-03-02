<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Configuration;
use App\Models\Panduan;
use Illuminate\Support\Facades\Storage;

class PublicConfigController extends Controller
{
    /**
     * Get important dates (public, authenticated)
     */
    public function getTanggalPenting()
    {
        $config = Configuration::where('key', 'tanggal_penting')->first();

        return response()->json([
            'success' => true,
            'data' => $config ? $config->value : [],
        ]);
    }

    /**
     * Get panduan list by type (mahasiswa, dosen, staff)
     */
    public function getPanduan($type)
    {
        if (!in_array($type, ['mahasiswa', 'dosen', 'staff'])) {
            return response()->json(['success' => false, 'message' => 'Tipe panduan tidak valid.'], 400);
        }

        $panduan = Panduan::where('type', $type)
            ->orderBy('created_at', 'desc')
            ->get()
            ->map(fn($p) => [
                'id' => $p->id,
                'nama_file' => $p->nama_file,
                'ukuran' => $p->ukuran,
                'created_at' => $p->created_at,
            ]);

        return response()->json([
            'success' => true,
            'data' => $panduan,
        ]);
    }

    /**
     * Download a specific panduan file
     */
    public function downloadPanduan($id)
    {
        $panduan = Panduan::findOrFail($id);

        if (!Storage::disk('public')->exists($panduan->file_path)) {
            return response()->json(['success' => false, 'message' => 'File tidak ditemukan.'], 404);
        }

        /** @var \Illuminate\Filesystem\FilesystemAdapter $disk */
        $disk = Storage::disk('public');

        return $disk->download($panduan->file_path, $panduan->nama_file);
    }
}
