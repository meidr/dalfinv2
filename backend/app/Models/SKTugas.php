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

    protected $appends = [
        'status',
        'status_color',
        'status_label'
    ];

    /**
     * Get the status attribute (Virtual)
     */
    public function getStatusAttribute()
    {
        return $this->file_sk ? 'selesai' : 'menunggu_ttd';
    }

    /**
     * Get the status color attribute
     */
    public function getStatusColorAttribute()
    {
        return $this->getColor($this->status);
    }

    /**
     * Get the status label attribute
     */
    public function getStatusLabelAttribute()
    {
        $labels = [
            'menunggu_ttd' => 'Menunggu TTD',
            'selesai' => 'Selesai',
        ];

        return $labels[$this->status] ?? $this->status;
    }

    /**
     * Get color based on status (Ref: SkripsiController)
     */
    private function getColor($status)
    {
        $colors = [
            'menunggu_ttd' => 'warning',
            'selesai' => 'success',
        ];

        return $colors[$status] ?? 'secondary';
    }

    /**
     * Get the skripsi
     */
    public function skripsi()
    {
        return $this->belongsTo(Skripsi::class);
    }
}
