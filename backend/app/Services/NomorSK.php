<?php

namespace App\Services;

use App\Models\NomorSK as ModelsNomorSK;
use Illuminate\Support\Facades\DB;

class NomorSK
{
    public function generate($jenisSk, $tahun, $prodi)
    {
        return "SK-" . $jenisSk . "-" . $tahun . "-" . $prodi;
    }

    // ini rencananya pakai API, tapi api belum ready jadi pakai static gini aja dulu
    public static function getNomorSk(){
        return 6;
    }
}
