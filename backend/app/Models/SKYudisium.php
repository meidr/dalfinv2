<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\Tahun;

class SKYudisium extends Model
{
    use HasFactory;

    protected $table = 'sk_yudisium';

    protected $fillable = [
        'skripsi_id',
        'nomor_sk',
        'nomor_sk_batch',
        'th_akademik_id',
        'prodi_id',
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

    /**
     * Get the tahun akademik
     */
    public function tahunAkademik()
    {
        return $this->belongsTo(Tahun::class, 'th_akademik_id');
    }

    /**
     * Get the prodi
     */
    public function prodi()
    {
        return $this->belongsTo(\App\Models\Prodi::class);
    }
}
