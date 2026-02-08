<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Dokumen extends Model
{
    use HasFactory;

    protected $table = 'dokumen';

    protected $fillable = [
        'skripsi_id',
        'jenis',
        'nama_file',
        'path',
        'ukuran',
        'versi',
        'status',
        'catatan',
        'uploaded_by',
    ];

    protected $casts = [
        'ukuran' => 'integer',
        'versi' => 'integer',
    ];

    const JENIS_PROPOSAL = 'proposal';
    const JENIS_BAB1 = 'bab1';
    const JENIS_BAB2 = 'bab2';
    const JENIS_BAB3 = 'bab3';
    const JENIS_BAB4 = 'bab4';
    const JENIS_BAB5 = 'bab5';
    const JENIS_FULL_DRAFT = 'full_draft';
    const JENIS_FINAL = 'final';
    const JENIS_REVISI = 'revisi';
    const JENIS_LAINNYA = 'lainnya';

    const STATUS_PENDING = 'pending';
    const STATUS_APPROVED = 'approved';
    const STATUS_REJECTED = 'rejected';

    /**
     * Get the skripsi
     */
    public function skripsi()
    {
        return $this->belongsTo(Skripsi::class);
    }

    /**
     * Get the uploader
     */
    public function uploader()
    {
        return $this->belongsTo(User::class, 'uploaded_by');
    }
}
