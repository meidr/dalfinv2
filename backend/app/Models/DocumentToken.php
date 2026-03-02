<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class DocumentToken extends Model
{
    protected $fillable = [
        'token',
        'document_type',
        'document_id',
        'nomor_surat',
        'nama_penandatangan',
        'jabatan_penandatangan',
        'nama_berkas',
        'metadata',
    ];

    protected $casts = [
        'metadata' => 'array',
    ];

    /**
     * Generate a unique token and create a DocumentToken record.
     */
    public static function generate(
        string $documentType,
        ?int $documentId,
        string $nomorSurat,
        string $namaPenandatangan,
        string $jabatanPenandatangan,
        string $namaBerkas,
        ?array $metadata = null
    ): self {
        return self::create([
            'token' => Str::random(64),
            'document_type' => $documentType,
            'document_id' => $documentId,
            'nomor_surat' => $nomorSurat,
            'nama_penandatangan' => $namaPenandatangan,
            'jabatan_penandatangan' => $jabatanPenandatangan,
            'nama_berkas' => $namaBerkas,
            'metadata' => $metadata,
        ]);
    }
}
