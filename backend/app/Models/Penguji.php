<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Penguji extends Model
{
    use HasFactory;

    protected $table = 'penguji';

    protected $fillable = [
        'seminar_id',
        'dosen_id',
        'peran',
        'nilai',
        'nilai_mt',
        'nilai_ms',
        'nilai_pm',
        'nilai_pi',
        'catatan',
    ];

    protected $casts = [
        'nilai' => 'decimal:2',
        'nilai_mt' => 'decimal:2',
        'nilai_ms' => 'decimal:2',
        'nilai_pm' => 'decimal:2',
        'nilai_pi' => 'decimal:2',
    ];

    const PERAN_KETUA = 'ketua';
    const PERAN_SEKRETARIS = 'sekretaris';
    const PERAN_ANGGOTA = 'anggota';

    /**
     * Get the seminar
     */
    public function seminar()
    {
        return $this->belongsTo(Seminar::class);
    }

    /**
     * Get the dosen
     */
    public function dosen()
    {
        return $this->belongsTo(Dosen::class);
    }
}
