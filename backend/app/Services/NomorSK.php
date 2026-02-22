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
}
