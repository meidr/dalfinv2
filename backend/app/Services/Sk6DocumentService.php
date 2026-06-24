<?php

namespace App\Services;

use App\Models\Dokumen;
use App\Models\Skripsi;
use Closure;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use RuntimeException;

class Sk6DocumentService
{
    public function storeForRequest(
        Skripsi $skripsi,
        UploadedFile $file,
        ?int $uploadedBy,
        Closure $completeRequest
    ): Dokumen {
        $storedPath = null;
        $oldPath = null;

        DB::beginTransaction();

        try {
            $existingDocument = Dokumen::where('skripsi_id', $skripsi->id)
                ->where('jenis', Dokumen::JENIS_SK6)
                ->lockForUpdate()
                ->first();

            $safeNim = preg_replace(
                '/[^A-Za-z0-9_-]/',
                '_',
                $skripsi->mahasiswa?->nim ?? "skripsi_{$skripsi->id}"
            );
            $filename = sprintf(
                '%s_SK6_%s_%s.%s',
                $safeNim,
                now()->format('YmdHis'),
                Str::lower(Str::random(6)),
                $file->getClientOriginalExtension()
            );
            $storedPath = $file->storeAs("dokumen/{$skripsi->id}", $filename, 'public');

            if (! $storedPath) {
                throw new RuntimeException('File SK 6 gagal disimpan.');
            }

            $oldPath = $existingDocument?->path;
            $document = Dokumen::updateOrCreate(
                [
                    'skripsi_id' => $skripsi->id,
                    'jenis' => Dokumen::JENIS_SK6,
                ],
                [
                    'nama_file' => $file->getClientOriginalName(),
                    'path' => $storedPath,
                    'ukuran' => $file->getSize(),
                    'versi' => ($existingDocument?->versi ?? 0) + 1,
                    'status' => Dokumen::STATUS_APPROVED,
                    'catatan' => 'Diunggah saat pengajuan sidang skripsi.',
                    'uploaded_by' => $uploadedBy,
                ]
            );

            $completeRequest();

            DB::commit();
        } catch (\Throwable $exception) {
            DB::rollBack();

            if ($storedPath) {
                Storage::disk('public')->delete($storedPath);
            }

            throw $exception;
        }

        if ($oldPath && $oldPath !== $storedPath) {
            Storage::disk('public')->delete($oldPath);
        }

        return $document;
    }
}
