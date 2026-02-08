<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Nilai extends Model
{
    use HasFactory;

    protected $table = 'nilai';

    protected $fillable = [
        'skripsi_id',
        'jenis',
        'nilai',
        'huruf_mutu',
        'keterangan',
        'penilai_id',
    ];

    protected $casts = [
        'nilai' => 'decimal:2',
    ];

    const JENIS_SEMPRO = 'sempro';
    const JENIS_SEMHAS = 'semhas';
    const JENIS_SIDANG = 'sidang';
    const JENIS_BIMBINGAN = 'bimbingan';
    const JENIS_FINAL = 'final';

    /**
     * Get the skripsi
     */
    public function skripsi()
    {
        return $this->belongsTo(Skripsi::class);
    }

    /**
     * Get the penilai (grader)
     */
    public function penilai()
    {
        return $this->belongsTo(Dosen::class, 'penilai_id');
    }

    /**
     * Calculate huruf mutu from nilai
     */
    public static function calculateHurufMutu(float $nilai): string
    {
        if ($nilai >= 85) return 'A';
        if ($nilai >= 80) return 'A-';
        if ($nilai >= 75) return 'B+';
        if ($nilai >= 70) return 'B';
        if ($nilai >= 65) return 'B-';
        if ($nilai >= 60) return 'C+';
        if ($nilai >= 55) return 'C';
        if ($nilai >= 50) return 'D';
        return 'E';
    }
}
