<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Tahun extends Model
{
    protected $fillable = [
        'name',
        'kode',
        'semester',
        'is_active',
    ];

    protected $casts = [
        'is_active' => 'boolean',
    ];

    /**
     * Get all mahasiswa in this tahun
     */
    public function mahasiswa()
    {
        return $this->hasMany(Mahasiswa::class);
    }
}
