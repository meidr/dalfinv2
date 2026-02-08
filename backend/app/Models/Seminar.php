<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Seminar extends Model
{
    use HasFactory;

    protected $table = 'seminar';

    protected $fillable = [
        'skripsi_id',
        'jenis',
        'tanggal',
        'waktu',
        'ruangan',
        'status',
        'nilai',
        'catatan',
    ];

    protected $casts = [
        'tanggal' => 'date',
        'waktu' => 'datetime:H:i',
        'nilai' => 'decimal:2',
    ];

    const JENIS_SEMPRO = 'sempro';
    const JENIS_SEMHAS = 'semhas';

    const STATUS_TERJADWAL = 'terjadwal';
    const STATUS_BERLANGSUNG = 'berlangsung';
    const STATUS_SELESAI = 'selesai';
    const STATUS_BATAL = 'batal';

    /**
     * Get the skripsi
     */
    public function skripsi()
    {
        return $this->belongsTo(Skripsi::class);
    }

    /**
     * Get penguji
     */
    public function penguji()
    {
        return $this->hasMany(Penguji::class);
    }

    /**
     * Get dosen penguji via pivot
     */
    public function dosenPenguji()
    {
        return $this->belongsToMany(Dosen::class, 'penguji')
            ->withPivot('peran', 'nilai', 'catatan')
            ->withTimestamps();
    }

    /**
     * Get berita acara
     */
    public function beritaAcara()
    {
        return $this->hasOne(BeritaAcara::class);
    }
}
