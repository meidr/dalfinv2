<?php
require __DIR__ . '/vendor/autoload.php';
$app = require_once __DIR__ . '/bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

use App\Models\Skripsi;

// Pick first lulus skripsi
$s = Skripsi::where('status', 'lulus')->first();
if (!$s) {
    echo "No lulus skripsi";
    exit;
}

$s->load([
    'seminar.penguji.dosen',
    'seminar.beritaAcara',
]);

echo "Skripsi ID: " . $s->id . "\n";
echo "Seminar count: " . $s->seminar->count() . "\n\n";

foreach ($s->seminar as $sem) {
    echo "Seminar ID: " . $sem->id . "\n";
    echo "  jenis: " . $sem->jenis . "\n";
    echo "  status: " . $sem->status . "\n";
    echo "  hasil: " . ($sem->hasil ?? "null") . "\n";
    echo "  penguji count: " . $sem->penguji->count() . "\n";
    $ba = $sem->beritaAcara;
    echo "  beritaAcara: " . ($ba ? "ID=" . $ba->id : "null") . "\n";
    // Check the JSON key
    echo "  JSON key check: ";
    $json = $sem->toArray();
    echo isset($json['berita_acara']) ? "berita_acara EXISTS" : "berita_acara MISSING";
    echo "\n\n";
}
