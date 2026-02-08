<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SKYudisium extends Model
{
    use HasFactory;

    protected $table = 'sk_yudisium';

    protected $fillable = [
        'skripsi_id',
        'nomor_sk',
        'tanggal_terbit',
        'tanggal_yudisium',
        'predikat',
        'ipk_akhir',
        'file_sk',
        'keterangan',
    ];

    protected $casts = [
        'tanggal_terbit' => 'date',
        'tanggal_yudisium' => 'date',
        'ipk_akhir' => 'decimal:2',
    ];

    /**
     * Get the skripsi
     */
    public function skripsi()
    {
        return $this->belongsTo(Skripsi::class);
    }
}
