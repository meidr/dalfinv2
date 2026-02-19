<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class JabatanPejabat extends Model
{
    protected $table = 'jabatan_pejabat';

    protected $fillable = [
        'periode_id',
        'jabatan_id',
        'dosen_id',
        'prodi_id',
        'fakultas_id',
        'tgl_mulai',
        'tgl_selesai',
        'is_plt',
        'keterangan',
    ];

    protected $casts = [
        'tgl_mulai' => 'date',
        'tgl_selesai' => 'date',
        'is_plt' => 'boolean',
    ];

    public function periode()
    {
        return $this->belongsTo(PeriodeJabatan::class, 'periode_id');
    }

    public function jabatan()
    {
        return $this->belongsTo(MasterJabatan::class, 'jabatan_id');
    }

    public function dosen()
    {
        return $this->belongsTo(Dosen::class);
    }

    public function prodi()
    {
        return $this->belongsTo(Prodi::class);
    }

    public function fakultas()
    {
        return $this->belongsTo(Fakultas::class);
    }

    /**
     * Get display title: prepend "Plt." if is_plt
     */
    public function getDisplayTitleAttribute(): string
    {
        $title = $this->jabatan->nama ?? '';
        return $this->is_plt ? "Plt. {$title}" : $title;
    }
}
