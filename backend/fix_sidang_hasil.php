<?php
require __DIR__ . '/vendor/autoload.php';
$app = require_once __DIR__ . '/bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

use App\Models\Skripsi;
use App\Models\Seminar;

// Find skripsi that are already 'lulus' but have sidang seminar with hasil='lulus_revisi'
$lulusSkripsi = Skripsi::where('status', 'lulus')->pluck('id');

$updated = Seminar::whereIn('skripsi_id', $lulusSkripsi)
    ->where('jenis', 'sidang')
    ->where('hasil', 'lulus_revisi')
    ->update(['hasil' => 'lulus']);

echo "Updated {$updated} sidang seminar records from lulus_revisi to lulus\n";
