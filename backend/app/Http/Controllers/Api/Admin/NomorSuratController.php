<?php

namespace App\Http\Controllers\Api\Admin;

use App\Http\Controllers\Controller;
use App\Models\NomorSuratTemplate;
use App\Services\NomorSuratService;
use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;

class NomorSuratController extends Controller
{
    public function index()
    {
        $items = NomorSuratTemplate::orderBy('id')->get();

        return response()->json([
            'success' => true,
            'data' => $items,
            'tokens' => NomorSuratService::ALLOWED_TOKENS,
        ]);
    }

    public function update(Request $request, NomorSuratTemplate $nomorSurat)
    {
        $validated = $request->validate([
            'template' => 'required|string|max:255',
            'level' => 'required|in:fakultas,prodi',
            'digit_urut' => 'required|integer|min:1|max:10',
        ]);

        $errors = NomorSuratService::validateTemplate($validated['template']);
        if (!empty($errors)) {
            throw ValidationException::withMessages([
                'template' => $errors,
            ]);
        }

        $nomorSurat->update($validated);

        return response()->json([
            'success' => true,
            'message' => 'Template nomor surat berhasil diperbarui',
            'data' => $nomorSurat->fresh(),
        ]);
    }
}
