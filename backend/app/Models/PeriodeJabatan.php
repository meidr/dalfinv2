<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PeriodeJabatan extends Model
{
    protected $table = 'periode_jabatan';

    protected $fillable = [
        'nama',
        'tgl_mulai',
        'tgl_selesai',
        'is_active',
    ];

    protected $casts = [
        'tgl_mulai' => 'date',
        'tgl_selesai' => 'date',
        'is_active' => 'boolean',
    ];

    public function pejabat()
    {
        return $this->hasMany(JabatanPejabat::class, 'periode_id');
    }
}
