<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\Tahun;

class Skripsi extends Model
{
    use HasFactory;

    protected $table = 'skripsi';

    protected $fillable = [
        'mahasiswa_id',
        'th_akademik_id',
        'judul',
        'abstrak',
        'kata_kunci',
        'status',
        'tanggal_daftar',
        'semester_daftar',
        'progress_percentage',
        'catatan_admin',
        'is_active',
        'file_skripsi',
        'alasan_tolak_sidang',
    ];

    protected $casts = [
        'is_active' => 'boolean',
        'progress_percentage' => 'integer',
        'tanggal_daftar' => 'date',
    ];

    protected $appends = ['file_skripsi_url'];

    /**
     * Get the full URL for the uploaded file_skripsi
     */
    public function getFileSkripsiUrlAttribute()
    {
        if (!$this->file_skripsi) {
            return null;
        }
        return url('/api/file/' . $this->file_skripsi);
    }

    /**
     * Status constants
     */
    const STATUS_DRAFT = 'draft';
    const STATUS_PENGAJUAN = 'pengajuan';
    const STATUS_DISETUJUI = 'disetujui';
    const STATUS_DITOLAK = 'ditolak';
    const STATUS_PROPOSAL = 'proposal';
    const STATUS_SEMPRO = 'sempro';
    const STATUS_PENENTUAN_MENTOR = 'penentuan_mentor';
    const STATUS_MENTOR = 'mentor';
    const STATUS_PENENTUAN_DOSPEM = 'penentuan_dospem';
    const STATUS_DOSPEM = 'dospem';
    const STATUS_BIMBINGAN = 'bimbingan';
    const STATUS_PENGAJUAN_SIDANG = 'pengajuan_sidang';
    const STATUS_PENGAJUAN_SIDANG_ACC = 'pengajuan_sidang_acc';
    const STATUS_PENGAJUAN_SIDANG_TOLAK = 'pengajuan_sidang_tolak';
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
     * Get tahun akademik
     */
    public function tahunAkademik()
    {
        return $this->belongsTo(Tahun::class, 'th_akademik_id');
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
     * Get all mentor sempro
     */
    public function mentorSempro()
    {
        return $this->hasMany(MentorSempro::class);
    }

    /**
     * Get mentor 1
     */
    public function mentor1()
    {
        return $this->hasOne(MentorSempro::class)->where('jenis', 'mentor_1');
    }

    /**
     * Get mentor 2
     */
    public function mentor2()
    {
        return $this->hasOne(MentorSempro::class)->where('jenis', 'mentor_2');
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
    public function logHistory(?string $oldTitle = null, ?string $oldStatus = null, ?string $reason = null, ?User $user = null)
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

    /**
     * Get similarity records where this skripsi is the source
     */
    public function similarities()
    {
        return $this->hasMany(SkripsiSimilarity::class, 'skripsi_id');
    }
}
