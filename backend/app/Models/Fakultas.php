<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Fakultas extends Model
{
    use HasFactory;

    protected $table = 'fakultas';

    protected $fillable = [
        'kode',
        'nama_fakultas',
        'dekan_id',
        'wakil_dekan_id',
        'is_active',
    ];

    protected $casts = [
        'is_active' => 'boolean',
    ];

    /**
     * Get the Dekan (Dosen)
     */
    public function dekan()
    {
        return $this->belongsTo(Dosen::class, 'dekan_id');
    }

    /**
     * Get the Wakil Dekan (Dosen)
     */
    public function wakilDekan()
    {
        return $this->belongsTo(Dosen::class, 'wakil_dekan_id');
    }

    /**
     * Get all prodi in this fakultas
     */
    public function prodi()
    {
        return $this->hasMany(Prodi::class);
    }
}
