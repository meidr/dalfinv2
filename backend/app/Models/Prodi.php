<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Prodi extends Model
{
    use HasFactory;

    protected $table = 'prodi';

    protected $fillable = [
        'kode',
        'nama',
        'fakultas',
        'jenjang',
        'is_active',
    ];

    protected $casts = [
        'is_active' => 'boolean',
    ];

    /**
     * Get all mahasiswa in this prodi
     */
    public function mahasiswa()
    {
        return $this->hasMany(Mahasiswa::class);
    }

    /**
     * Get all dosen in this prodi
     */
    public function dosen()
    {
        return $this->hasMany(Dosen::class);
    }
}
