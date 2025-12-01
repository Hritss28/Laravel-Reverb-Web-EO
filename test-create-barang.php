<?php

require_once 'vendor/autoload.php';

// Bootstrap Laravel
$app = require_once 'bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

use App\Models\Barang;
use App\Models\Kategori;

// Create a test barang
try {
    $kategori = Kategori::first();
    if (!$kategori) {
        echo "No categories found. Creating test category...\n";
        $kategori = Kategori::create([
            'nama' => 'Test Category',
            'deskripsi' => 'Test category for testing'
        ]);
    }

    $barang = Barang::create([
        'nama' => 'Test Barang ' . time(),
        'kode_barang' => 'TEST-' . time(),
        'kategori_id' => $kategori->id,
        'deskripsi' => 'Test barang for real-time testing',
        'kondisi' => 'baik',
        'lokasi' => 'Test Location',
        'stok' => 10,
        'harga_sewa_per_hari' => 50000,
        'biaya_deposit' => 100000,
    ]);

    echo "Created test barang: {$barang->nama} (ID: {$barang->id})\n";
    echo "This should trigger real-time updates on the frontend!\n";

} catch (Exception $e) {
    echo "Error: " . $e->getMessage() . "\n";
    echo "Stack trace:\n" . $e->getTraceAsString() . "\n";
}
