<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class BeritaAcara extends Model
{
    use HasFactory;

    protected $table = 'berita_acara';

    protected $fillable = [
        'jenis',
        'seminar_id',
        'ujian_id',
        'nomor',
        'tanggal',
        'hasil',
        'catatan',
        'file_ba',
    ];

    protected $casts = [
        'tanggal' => 'date',
    ];

    const JENIS_SEMINAR = 'seminar';
    const JENIS_UJIAN = 'ujian';

    const HASIL_LULUS = 'lulus';
    const HASIL_LULUS_BERSYARAT = 'lulus_bersyarat';
    const HASIL_TIDAK_LULUS = 'tidak_lulus';
    const HASIL_MENGULANG = 'mengulang';

    /**
     * Get the seminar
     */
    public function seminar()
    {
        return $this->belongsTo(Seminar::class);
    }

    /**
     * Get the ujian
     */
    public function ujian()
    {
        return $this->belongsTo(Ujian::class);
    }
}
