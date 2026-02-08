<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Skripsi extends Model
{
    use HasFactory;

    protected $table = 'skripsi';

    protected $fillable = [
        'mahasiswa_id',
        'judul',
        'abstrak',
        'kata_kunci',
        'status',
        'tanggal_daftar',
        'semester_daftar',
        'progress_percentage',
        'catatan_admin',
        'is_active',
    ];

    protected $casts = [
        'is_active' => 'boolean',
        'progress_percentage' => 'integer',
        'tanggal_daftar' => 'date',
    ];

    /**
     * Status constants
     */
    const STATUS_DRAFT = 'draft';
    const STATUS_PENGAJUAN = 'pengajuan';
    const STATUS_DISETUJUI = 'disetujui';
    const STATUS_DITOLAK = 'ditolak';
    const STATUS_PROPOSAL = 'proposal';
    const STATUS_SEMPRO = 'sempro';
    const STATUS_BIMBINGAN = 'bimbingan';
    const STATUS_SEMHAS = 'semhas';
    const STATUS_SIDANG = 'sidang';
    const STATUS_REVISI = 'revisi';
    const STATUS_LULUS = 'lulus';

    /**
     * Get the mahasiswa
     */
    public function mahasiswa()
    {
        return $this->belongsTo(Mahasiswa::class);
    }

    /**
     * Get all pembimbing
     */
    public function pembimbing()
    {
        return $this->hasMany(Pembimbing::class);
    }

    /**
     * Get dosen pembimbing via pivot
     */
    public function dosenPembimbing()
    {
        return $this->belongsToMany(Dosen::class, 'pembimbing')
            ->withPivot('jenis', 'tanggal_penetapan', 'is_active')
            ->withTimestamps();
    }

    /**
     * Get pembimbing 1
     */
    public function pembimbing1()
    {
        return $this->hasOne(Pembimbing::class)->where('jenis', 'pembimbing_1');
    }

    /**
     * Get pembimbing 2
     */
    public function pembimbing2()
    {
        return $this->hasOne(Pembimbing::class)->where('jenis', 'pembimbing_2');
    }

    /**
     * Get all bimbingan logs
     */
    public function bimbingan()
    {
        return $this->hasMany(Bimbingan::class);
    }

    /**
     * Get history
     */
    public function history()
    {
        return $this->hasMany(SkripsiHistory::class);
    }

    /**
     * Get seminars
     */
    public function seminar()
    {
        return $this->hasMany(Seminar::class);
    }

    /**
     * Get sempro
     */
    public function sempro()
    {
        return $this->hasOne(Seminar::class)->where('jenis', 'sempro');
    }

    /**
     * Get semhas
     */
    public function semhas()
    {
        return $this->hasOne(Seminar::class)->where('jenis', 'semhas');
    }

    /**
     * Get ujian/sidang
     */
    public function ujian()
    {
        return $this->hasOne(Ujian::class);
    }

    /**
     * Get all dokumen
     */
    public function dokumen()
    {
        return $this->hasMany(Dokumen::class);
    }

    /**
     * Get all nilai
     */
    public function nilai()
    {
        return $this->hasMany(Nilai::class);
    }

    /**
     * Get SK Tugas
     */
    public function skTugas()
    {
        return $this->hasOne(SKTugas::class);
    }

    /**
     * Get nota bimbingan
     */
    public function notaBimbingan()
    {
        return $this->hasOne(NotaBimbingan::class);
    }

    /**
     * Get SK Yudisium
     */
    public function skYudisium()
    {
        return $this->hasOne(SKYudisium::class);
    }

    /**
     * Log history on status/title change
     */
    public function logHistory(string $oldTitle = null, string $oldStatus = null, string $reason = null, User $user = null)
    {
        return $this->history()->create([
            'judul_lama' => $oldTitle,
            'judul_baru' => $this->judul,
            'status_lama' => $oldStatus,
            'status_baru' => $this->status,
            'alasan' => $reason,
            'updated_by' => $user?->id,
        ]);
    }
}
