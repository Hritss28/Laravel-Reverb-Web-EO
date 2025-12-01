<?php

require 'vendor/autoload.php';

use App\Livewire\BarangList;

// Bootstrap Laravel
$app = require 'bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

try {
    echo "=== Test Livewire Rendering ===" . PHP_EOL;
    
    $component = new BarangList();
    $component->mount();
    
    echo "✅ Component mounted" . PHP_EOL;
    
    // Test property getters
    $barangs = $component->getBarangsProperty();
    $kategoris = $component->getKategorisProperty();
    
    echo "Barangs count: " . $barangs->count() . PHP_EOL;
    echo "Total items: " . $barangs->total() . PHP_EOL;
    echo "Kategoris count: " . $kategoris->count() . PHP_EOL;
    
    echo PHP_EOL . "=== First 3 Barangs ===" . PHP_EOL;
    foreach ($barangs->take(3) as $barang) {
        echo "- " . $barang->nama . " (Stock: " . $barang->stok_tersedia . ")" . PHP_EOL;
    }
    
    echo PHP_EOL . "=== Categories ===" . PHP_EOL;
    foreach ($kategoris as $kategori) {
        echo "- " . $kategori->nama . PHP_EOL;
    }
    
    echo PHP_EOL . "✅ All property getters working!" . PHP_EOL;
    
} catch (Exception $e) {
    echo "❌ Error: " . $e->getMessage() . PHP_EOL;
    echo "File: " . $e->getFile() . " Line: " . $e->getLine() . PHP_EOL;
    echo "Stack trace:" . PHP_EOL;
    echo $e->getTraceAsString() . PHP_EOL;
}
