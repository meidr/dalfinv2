<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class NotaBimbingan extends Model
{
    use HasFactory;

    protected $table = 'nota_bimbingan';

    protected $fillable = [
        'skripsi_id',
        'nomor',
        'tanggal_terbit',
        'total_bimbingan',
        'file_nota',
        'keterangan',
    ];

    protected $casts = [
        'tanggal_terbit' => 'date',
        'total_bimbingan' => 'integer',
    ];

    /**
     * Get the skripsi
     */
    public function skripsi()
    {
        return $this->belongsTo(Skripsi::class);
    }
}
