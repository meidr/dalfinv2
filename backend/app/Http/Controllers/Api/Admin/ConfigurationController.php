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
}
