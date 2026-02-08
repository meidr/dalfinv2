<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Mahasiswa extends Model
{
    use HasFactory;

    protected $table = 'mahasiswa';

    protected $fillable = [
        'user_id',
        'nim',
        'nama',
        'prodi_id',
        'semester',
        'angkatan',
        'jenis_kelamin',
        'tempat_lahir',
        'tanggal_lahir',
        'no_hp',
        'alamat',
        'email',
        'is_active',
    ];

    protected $casts = [
        'is_active' => 'boolean',
        'semester' => 'integer',
        'tanggal_lahir' => 'date',
    ];

    /**
     * Get the user account
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Get the prodi
     */
    public function prodi()
    {
        return $this->belongsTo(Prodi::class);
    }

    /**
     * Get all skripsi
     */
    public function skripsi()
    {
        return $this->hasMany(Skripsi::class);
    }

    /**
     * Get active skripsi
     */
    public function activeSkripsi()
    {
        return $this->hasOne(Skripsi::class)->where('is_active', true);
    }
}
