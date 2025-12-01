<?php

require_once 'vendor/autoload.php';

// Bootstrap Laravel
$app = require_once 'bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

use App\Models\Barang;

// Update the last created barang
try {
    $barang = Barang::latest()->first();
    
    if (!$barang) {
        echo "No barang found to update.\n";
        exit;
    }

    $barang->update([
        'nama' => $barang->nama . ' (Updated)',
        'deskripsi' => 'Updated description: ' . $barang->deskripsi,
        'stok' => $barang->stok + 5,
    ]);

    echo "Updated barang: {$barang->nama} (ID: {$barang->id})\n";
    echo "This should trigger real-time updates on the frontend!\n";

} catch (Exception $e) {
    echo "Error: " . $e->getMessage() . "\n";
    echo "Stack trace:\n" . $e->getTraceAsString() . "\n";
}
