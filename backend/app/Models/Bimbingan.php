<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Bimbingan extends Model
{
    use HasFactory;

    protected $table = 'bimbingan';

    protected $fillable = [
        'skripsi_id',
        'dosen_id',
        'tanggal',
        'topik',
        'deskripsi',
        'catatan_dosen',
        'status',
        'file_bukti',
    ];

    protected $casts = [
        'tanggal' => 'date',
    ];

    const STATUS_PENDING = 'pending';
    const STATUS_APPROVED = 'approved';
    const STATUS_REVISION = 'revision';
    const STATUS_REJECTED = 'rejected';

    /**
     * Get the skripsi
     */
    public function skripsi()
    {
        return $this->belongsTo(Skripsi::class);
    }

    /**
     * Get the dosen
     */
    public function dosen()
    {
        return $this->belongsTo(Dosen::class);
    }
}
