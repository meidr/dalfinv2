<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SKTugas extends Model
{
    use HasFactory;

    protected $table = 'sk_tugas';

    protected $fillable = [
        'skripsi_id',
        'nomor_sk',
        'tanggal_terbit',
        'file_sk',
        'keterangan',
    ];

    protected $casts = [
        'tanggal_terbit' => 'date',
    ];

    /**
     * Get the skripsi
     */
    public function skripsi()
    {
        return $this->belongsTo(Skripsi::class);
    }
}
