<?php

namespace App\Http\Controllers\Api\Admin;

use App\Http\Controllers\Controller;
use App\Models\TandaTangan;
use App\Models\Dosen;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class TandaTanganController extends Controller
{
    /**
     * List all signatures with dosen info
     */
    public function index(Request $request)
    {
        $query = TandaTangan::with(['dosen']);

        if ($request->filled('search')) {
            $search = $request->search;
            $query->whereHas('dosen', function ($q) use ($search) {
                $q->where('nama', 'like', "%{$search}%")
                    ->orWhere('nip', 'like', "%{$search}%");
            });
        }

        $data = $query->orderBy('created_at', 'desc')->get();

        return response()->json([
            'success' => true,
            'data' => $data,
        ]);
    }

    /**
     * Store a new signature (upload or drawn base64)
     */
    public function store(Request $request)
    {
        $request->validate([
            'dosen_id' => 'required|exists:dosen,id|unique:tanda_tangan,dosen_id',
            'ttd_file' => 'nullable|image|mimes:png,jpg,jpeg|max:2048',
            'ttd_base64' => 'nullable|string',
        ]);

        if (!$request->hasFile('ttd_file') && !$request->filled('ttd_base64')) {
            return response()->json([
                'success' => false,
                'message' => 'Harap upload file tanda tangan atau gambar tanda tangan.',
            ], 422);
        }

        $path = $this->saveSignature($request);

        $ttd = TandaTangan::create([
            'dosen_id' => $request->dosen_id,
            'ttd' => $path,
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Tanda tangan berhasil disimpan',
            'data' => $ttd->load('dosen'),
        ], 201);
    }

    /**
     * Update an existing signature
     */
    public function update(Request $request, TandaTangan $tanda_tangan)
    {
        $request->validate([
            'dosen_id' => 'sometimes|exists:dosen,id|unique:tanda_tangan,dosen_id,' . $tanda_tangan->id,
            'ttd_file' => 'nullable|image|mimes:png,jpg,jpeg|max:2048',
            'ttd_base64' => 'nullable|string',
        ]);

        if ($request->hasFile('ttd_file') || $request->filled('ttd_base64')) {
            // Delete old file
            if ($tanda_tangan->ttd && Storage::disk('public')->exists($tanda_tangan->ttd)) {
                Storage::disk('public')->delete($tanda_tangan->ttd);
            }
            $tanda_tangan->ttd = $this->saveSignature($request);
        }

        if ($request->filled('dosen_id')) {
            $tanda_tangan->dosen_id = $request->dosen_id;
        }

        $tanda_tangan->save();

        return response()->json([
            'success' => true,
            'message' => 'Tanda tangan berhasil diperbarui',
            'data' => $tanda_tangan->load('dosen'),
        ]);
    }

    /**
     * Delete a signature
     */
    public function destroy(TandaTangan $tanda_tangan)
    {
        if ($tanda_tangan->ttd && Storage::disk('public')->exists($tanda_tangan->ttd)) {
            Storage::disk('public')->delete($tanda_tangan->ttd);
        }

        $tanda_tangan->delete();

        return response()->json([
            'success' => true,
            'message' => 'Tanda tangan berhasil dihapus',
        ]);
    }

    /**
     * Save the signature file from either upload or base64 canvas drawing
     */
    private function saveSignature(Request $request): string
    {
        if ($request->hasFile('ttd_file')) {
            return $request->file('ttd_file')->store('tanda_tangan', 'public');
        }

        // Base64 drawn signature
        $base64 = $request->ttd_base64;
        // Remove data URI prefix if present
        if (str_contains($base64, ',')) {
            $base64 = explode(',', $base64)[1];
        }

        $image = base64_decode($base64);
        $filename = 'tanda_tangan/' . uniqid('ttd_') . '.png';
        Storage::disk('public')->put($filename, $image);

        return $filename;
    }
}
