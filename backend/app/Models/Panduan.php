<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Panduan extends Model
{
    protected $table = 'panduan';

    protected $fillable = ['type', 'nama_file', 'file_path', 'ukuran', 'uploaded_by'];

    public function uploader()
    {
        return $this->belongsTo(User::class, 'uploaded_by');
    }
}
