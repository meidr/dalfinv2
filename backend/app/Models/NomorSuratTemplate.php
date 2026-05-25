<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class NomorSuratTemplate extends Model
{
    use HasFactory;

    protected $fillable = [
        'key',
        'nama',
        'level',
        'template',
        'digit_urut',
        'is_active',
    ];

    protected $casts = [
        'digit_urut' => 'integer',
        'is_active' => 'boolean',
    ];
}
