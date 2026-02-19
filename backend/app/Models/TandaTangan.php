<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class TandaTangan extends Model
{
    protected $table = 'tanda_tangan';

    protected $fillable = [
        'dosen_id',
        'ttd',
    ];

    /**
     * Get the dosen that owns this signature
     */
    public function dosen()
    {
        return $this->belongsTo(Dosen::class);
    }

    /**
     * Get the full URL to the signature image
     */
    public function getTtdUrlAttribute()
    {
        if (!$this->ttd) return null;
        return asset('storage/' . $this->ttd);
    }

    protected $appends = ['ttd_url'];
}
