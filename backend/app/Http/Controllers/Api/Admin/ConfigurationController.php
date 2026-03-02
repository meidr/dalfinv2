<?php

namespace App\Http\Controllers\Api\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Configuration;

class ConfigurationController extends Controller
{
    /**
     * Get SK Tugas Signer Configuration
     */
    public function getSkTugasSigner()
    {
        $config = Configuration::where('key', 'sk_tugas_signer')->first();

        if (!$config) {
            return response()->json([
                'success' => true,
                'data' => [
                    'name' => '',
                    'nip' => '',
                    'position' => 'Kepala Prodi',
                    'city' => 'Bangil',
                    'institution' => 'Universitas Islam Internasional Darullughah Wadda\'wah',
                    'signature' => null,
                    'header_image' => null,
                    'stamp_image' => null,
                ]
            ]);
        }

        return response()->json([
            'success' => true,
            'data' => $config->value
        ]);
    }

    /**
     * Save SK Tugas Signer Configuration
     */
    public function saveSkTugasSigner(Request $request)
    {
        $request->validate([
            'name' => 'required|string',
            'nip' => 'required|string',
            'position' => 'required|string',
            'city' => 'required|string',
            'institution' => 'nullable|string',
            'signature' => 'nullable|string', // Base64
            'header_image' => 'nullable|string', // Base64
            'stamp_image' => 'nullable|string', // Base64
        ]);

        $data = $request->only(['name', 'nip', 'position', 'city', 'institution', 'signature', 'header_image', 'stamp_image']);

        $config = Configuration::updateOrCreate(
            ['key' => 'sk_tugas_signer'],
            ['value' => $data]
        );

        return response()->json([
            'success' => true,
            'message' => 'Konfigurasi berhasil disimpan',
            'data' => $config->value
        ]);
    }

    /**
     * Get Syarat Bimbingan Ujian Configuration
     */
    public function getSyaratBimbingan()
    {
        $config = Configuration::where('key', 'syarat_bimbingan_ujian')->first();

        return response()->json([
            'success' => true,
            'data' => $config ? $config->value : ['pembimbing_1' => 8, 'pembimbing_2' => 4]
        ]);
    }

    /**
     * Save Syarat Bimbingan Ujian Configuration
     */
    public function saveSyaratBimbingan(Request $request)
    {
        $request->validate([
            'pembimbing_1' => 'required|integer|min:1',
            'pembimbing_2' => 'required|integer|min:1',
        ]);

        $config = Configuration::updateOrCreate(
            ['key' => 'syarat_bimbingan_ujian'],
            ['value' => $request->only(['pembimbing_1', 'pembimbing_2'])]
        );

        return response()->json([
            'success' => true,
            'message' => 'Syarat bimbingan berhasil disimpan',
            'data' => $config->value
        ]);
    }

    /**
     * Get Kuota Bimbingan Default Configuration
     */
    public function getKuotaBimbingan()
    {
        $config = Configuration::where('key', 'kuota_bimbingan_default')->first();

        return response()->json([
            'success' => true,
            'data' => $config ? $config->value : ['kuota' => 10]
        ]);
    }

    /**
     * Save Kuota Bimbingan Default Configuration
     */
    public function saveKuotaBimbingan(Request $request)
    {
        $request->validate([
            'kuota' => 'required|integer|min:1|max:50',
        ]);

        $config = Configuration::updateOrCreate(
            ['key' => 'kuota_bimbingan_default'],
            ['value' => ['kuota' => (int) $request->kuota]]
        );

        return response()->json([
            'success' => true,
            'message' => 'Kuota bimbingan default berhasil disimpan',
            'data' => $config->value
        ]);
    }

    /**
     * Get Tanggal Penting Configuration
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
     * Save Tanggal Penting Configuration
     */
    public function saveTanggalPenting(Request $request)
    {
        $request->validate([
            'dates' => 'required|array',
            'dates.*.label' => 'required|string|max:255',
            'dates.*.tanggal' => 'required|date',
        ]);

        $config = Configuration::updateOrCreate(
            ['key' => 'tanggal_penting'],
            ['value' => $request->dates]
        );

        return response()->json([
            'success' => true,
            'message' => 'Tanggal penting berhasil disimpan',
            'data' => $config->value,
        ]);
    }

    /**
     * Upload a panduan file
     */
    public function uploadPanduan(Request $request, $type)
    {
        if (!in_array($type, ['mahasiswa', 'dosen', 'staff'])) {
            return response()->json(['success' => false, 'message' => 'Tipe panduan tidak valid.'], 400);
        }

        $request->validate([
            'file' => 'required|file|max:10240|mimes:pdf,doc,docx,xls,xlsx,ppt,pptx',
        ]);

        $file = $request->file('file');
        $path = $file->store("panduan/{$type}", 'public');

        $panduan = \App\Models\Panduan::create([
            'type' => $type,
            'nama_file' => $file->getClientOriginalName(),
            'file_path' => $path,
            'ukuran' => $file->getSize(),
            'uploaded_by' => $request->user()->id,
        ]);

        return response()->json([
            'success' => true,
            'message' => 'File panduan berhasil diunggah',
            'data' => [
                'id' => $panduan->id,
                'nama_file' => $panduan->nama_file,
                'ukuran' => $panduan->ukuran,
                'created_at' => $panduan->created_at,
            ],
        ]);
    }

    /**
     * Get list of panduan files by type
     */
    public function getPanduanList($type)
    {
        if (!in_array($type, ['mahasiswa', 'dosen', 'staff'])) {
            return response()->json(['success' => false, 'message' => 'Tipe panduan tidak valid.'], 400);
        }

        $panduan = \App\Models\Panduan::where('type', $type)
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
     * Delete a panduan file
     */
    public function deletePanduan($id)
    {
        $panduan = \App\Models\Panduan::findOrFail($id);

        \Illuminate\Support\Facades\Storage::disk('public')->delete($panduan->file_path);
        $panduan->delete();

        return response()->json([
            'success' => true,
            'message' => 'File panduan berhasil dihapus',
        ]);
    }
    /**
     * Get Jenis Tanda Tangan Configuration
     */
    public function getJenisTtd()
    {
        $config = Configuration::where('key', 'jenis_ttd')->first();

        return response()->json([
            'success' => true,
            'data' => $config ? $config->value : ['jenis' => 'biasa'],
        ]);
    }

    /**
     * Save Jenis Tanda Tangan Configuration
     */
    public function saveJenisTtd(Request $request)
    {
        $request->validate([
            'jenis' => 'required|string|in:biasa,qr',
        ]);

        $config = Configuration::updateOrCreate(
            ['key' => 'jenis_ttd'],
            ['value' => ['jenis' => $request->jenis]]
        );

        return response()->json([
            'success' => true,
            'message' => 'Jenis tanda tangan berhasil disimpan',
            'data' => $config->value,
        ]);
    }
}
