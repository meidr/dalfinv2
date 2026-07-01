<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class LembarPengesahan extends Model
{
    use HasFactory;

    protected $table = 'lembar_pengesahan';
    protected $guarded = [];
    protected $casts = [
        'tanggal' => 'date',
    ];

    public function seminar()
    {
        return $this->belongsTo(Seminar::class);
    }
}
