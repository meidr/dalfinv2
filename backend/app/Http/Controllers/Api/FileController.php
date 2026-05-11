<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Storage;

class FileController extends Controller
{
    /**
     * Serve a file from the public storage disk.
     *
     * This endpoint streams files through PHP, bypassing nginx symlink issues
     * that cause 403 Forbidden errors on production servers.
     *
     * @param string $path Relative path within the public disk
     */
    public function serve(string $path)
    {
        $disk = Storage::disk('public');

        if (!$disk->exists($path)) {
            abort(404, 'File tidak ditemukan');
        }

        $mimeType = $disk->mimeType($path) ?: 'application/octet-stream';
        $fileName = basename($path);

        return $disk->response($path, $fileName, [
            'Content-Type' => $mimeType,
            'Cache-Control' => 'public, max-age=86400',
        ]);
    }
}
