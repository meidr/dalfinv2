<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PengujiUjian extends Model
{
    use HasFactory;

    protected $table = 'penguji_ujian';

    protected $fillable = [
        'ujian_id',
        'dosen_id',
        'peran',
        'nilai',
        'catatan',
    ];

    protected $casts = [
        'nilai' => 'decimal:2',
    ];

    /**
     * Get the ujian
     */
    public function ujian()
    {
        return $this->belongsTo(Ujian::class);
    }

    /**
     * Get the dosen
     */
    public function dosen()
    {
        return $this->belongsTo(Dosen::class);
    }
}
