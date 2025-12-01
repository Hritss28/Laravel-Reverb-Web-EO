<?php

require_once 'vendor/autoload.php';

// Bootstrap Laravel
$app = require_once 'bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

use App\Models\Barang;

// Delete a test barang
try {
    $barang = Barang::where('nama', 'like', 'Test Barang%')->latest()->first();
    
    if (!$barang) {
        echo "No test barang found to delete.\n";
        exit;
    }

    $barangName = $barang->nama;
    $barangId = $barang->id;
    
    $barang->delete();

    echo "Deleted barang: {$barangName} (ID: {$barangId})\n";
    echo "This should trigger real-time deletion updates on the frontend!\n";

} catch (Exception $e) {
    echo "Error: " . $e->getMessage() . "\n";
    echo "Stack trace:\n" . $e->getTraceAsString() . "\n";
}
