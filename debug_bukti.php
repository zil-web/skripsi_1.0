<?php
require 'bootstrap/app.php';
$app->make(\Illuminate\Contracts\Console\Kernel::class)->bootstrap();

use App\Models\Transaksi;
use Illuminate\Support\Facades\Storage;

$transaksis = Transaksi::whereNotNull('bukti_transaksi')->take(5)->get();

foreach ($transaksis as $t) {
    $raw = $t->bukti_transaksi;
    $url = Storage::disk('public')->url($raw);
    $exists = Storage::disk('public')->exists($raw);
    
    echo "ID: {$t->id}\n";
    echo "  Raw: {$raw}\n";
    echo "  URL: {$url}\n";
    echo "  Exists: " . ($exists ? 'YES' : 'NO') . "\n";
    echo "  File path: " . storage_path('app/public/' . $raw) . "\n";
    echo "  File exists on disk: " . (file_exists(storage_path('app/public/' . $raw)) ? 'YES' : 'NO') . "\n";
    echo "\n";
}
