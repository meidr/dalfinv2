<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SkripsiHistory extends Model
{
    use HasFactory;

    protected $table = 'skripsi_history';

    protected $fillable = [
        'skripsi_id',
        'judul_lama',
        'judul_baru',
        'status_lama',
        'status_baru',
        'alasan',
        'keterangan',
        'updated_by',
    ];

    /**
     * Get the skripsi
     */
    public function skripsi()
    {
        return $this->belongsTo(Skripsi::class);
    }

    /**
     * Get the user who made the update
     */
    public function updatedBy()
    {
        return $this->belongsTo(User::class, 'updated_by');
    }
}
