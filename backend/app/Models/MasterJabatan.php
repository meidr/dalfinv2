<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class MasterJabatan extends Model
{
    protected $table = 'master_jabatan';

    protected $fillable = [
        'kode',
        'nama',
        'level',
    ];

    public function pejabat()
    {
        return $this->hasMany(JabatanPejabat::class, 'jabatan_id');
    }
}
