<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Dosen extends Model
{
    use HasFactory;

    protected $table = 'dosen';

    protected $fillable = [
        'user_id',
        'nip',
        'nama',
        'gelar_depan',
        'gelar_belakang',
        'jabatan_fungsional',
        'bidang_keahlian',
        'prodi_id',
        'email',
        'no_hp',
        'jenis_kelamin',
        'is_active',
        'kuota_bimbingan',
    ];

    protected $casts = [
        'is_active' => 'boolean',
        'kuota_bimbingan' => 'integer',
    ];

    protected $appends = ['full_name'];

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
     * Get all pembimbing assignments
     */
    public function pembimbing()
    {
        return $this->hasMany(Pembimbing::class);
    }

    /**
     * Get all bimbingan sessions
     */
    public function bimbingan()
    {
        return $this->hasMany(Bimbingan::class);
    }

    /**
     * Get all skripsi as pembimbing
     */
    public function skripsiAsPembimbing()
    {
        return $this->belongsToMany(Skripsi::class, 'pembimbing')
            ->withPivot('jenis', 'tanggal_penetapan', 'is_active')
            ->withTimestamps();
    }

    /**
     * Get full name with titles
     */
    public function getFullNameAttribute(): string
    {
        $name = '';
        if ($this->gelar_depan) {
            $name .= $this->gelar_depan . ' ';
        }
        $name .= $this->nama;
        if ($this->gelar_belakang) {
            $name .= ', ' . $this->gelar_belakang;
        }
        return $name;
    }

    /**
     * Get current bimbingan count
     */
    public function getCurrentBimbinganCountAttribute(): int
    {
        return $this->pembimbing()->where('is_active', true)->count();
    }

    /**
     * Check if dosen has available slot for bimbingan
     */
    public function hasAvailableSlot(): bool
    {
        return $this->current_bimbingan_count < $this->kuota_bimbingan;
    }
}
