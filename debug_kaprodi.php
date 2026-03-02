<?php
require 'c:/laragon/www/skripsi/backend/vendor/autoload.php';
$app = require_once 'c:/laragon/www/skripsi/backend/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

// Test resolveKaprodi directly
$s = \App\Models\Skripsi::with('mahasiswa.prodi.fakultas')->where('is_active', true)->first();
if (!$s) {
    echo "No active skripsi\n";
    exit;
}

$prodi = $s->mahasiswa->prodi;
echo "Mahasiswa: {$s->mahasiswa->nama}\n";
echo "prodi_id: {$prodi->id}, prodi: {$prodi->nama}\n\n";

// Step 1
$jabatan = \App\Models\MasterJabatan::where('kode', 'KAPRODI')->first();
echo "Step 1 - MasterJabatan KAPRODI: " . ($jabatan ? "id={$jabatan->id}" : "NOT FOUND") . "\n";

// Step 2
$periode = \App\Models\PeriodeJabatan::where('is_active', true)->first();
echo "Step 2 - Active PeriodeJabatan: " . ($periode ? "id={$periode->id}" : "NOT FOUND") . "\n";

// Step 3
if ($jabatan && $periode) {
    $today = now()->toDateString();
    echo "Step 3 - Query: jabatan_id={$jabatan->id}, periode_id={$periode->id}, prodi_id={$prodi->id}, today={$today}\n";

    $pejabat = \App\Models\JabatanPejabat::with('dosen')
        ->where('periode_id', $periode->id)
        ->where('jabatan_id', $jabatan->id)
        ->where('prodi_id', $prodi->id)
        ->where(function ($q) use ($today) {
            $q->whereNull('tgl_selesai')
                ->orWhere('tgl_selesai', '>=', $today);
        })
        ->first();

    if ($pejabat) {
        echo "FOUND: dosen=" . ($pejabat->dosen ? $pejabat->dosen->nama : 'null') . "\n";
    } else {
        echo "NOT FOUND!\n";
        // Check without date filter
        $pejabat2 = \App\Models\JabatanPejabat::with('dosen')
            ->where('periode_id', $periode->id)
            ->where('jabatan_id', $jabatan->id)
            ->where('prodi_id', $prodi->id)
            ->first();
        echo "  Without date filter: " . ($pejabat2 ? "FOUND dosen=" . ($pejabat2->dosen ? $pejabat2->dosen->nama : 'null') . " tgl_selesai={$pejabat2->tgl_selesai}" : "STILL NOT FOUND") . "\n";

        // Check without prodi filter
        $pejabat3 = \App\Models\JabatanPejabat::with('dosen')
            ->where('periode_id', $periode->id)
            ->where('jabatan_id', $jabatan->id)
            ->get();
        echo "  Without prodi filter: {$pejabat3->count()} records\n";
        foreach ($pejabat3 as $p3) {
            echo "    prodi_id={$p3->prodi_id} dosen=" . ($p3->dosen ? $p3->dosen->nama : 'null') . "\n";
        }
    }
}
