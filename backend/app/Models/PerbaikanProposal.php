<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PerbaikanProposal extends Model
{
    use HasFactory;

    protected $table = 'perbaikan_proposal';

    protected $fillable = [
        'seminar_id',
        'no',
        'topik',
        'halaman',
        'uraian',
    ];

    public function seminar()
    {
        return $this->belongsTo(Seminar::class);
    }
}
