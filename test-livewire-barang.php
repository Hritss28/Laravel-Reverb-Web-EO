<?php

require __DIR__ . '/vendor/autoload.php';

$app = require_once __DIR__ . '/bootstrap/app.php';
$app->make(\Illuminate\Contracts\Console\Kernel::class)->bootstrap();

use App\Livewire\BarangList;

echo "=== Testing BarangList Component ===\n\n";

// Instantiate the component
$component = new BarangList();
$component->mount();

echo "Search: '{$component->search}'\n";
echo "Kategori: '{$component->kategori}'\n";
echo "Kondisi: '{$component->kondisi}'\n";
echo "PerPage: {$component->perPage}\n\n";

// Test barangs computed property
echo "=== Testing barangs() method ===\n";
try {
    $barangs = $component->barangs();
    echo "Barangs count: " . $barangs->count() . "\n";
    echo "Barangs total: " . $barangs->total() . "\n";
    
    if ($barangs->count() > 0) {
        echo "\nFirst 3 items:\n";
        foreach ($barangs->take(3) as $b) {
            echo "- {$b->nama} (ID: {$b->id}, Kategori: " . ($b->kategori ? $b->kategori->nama : 'NULL') . ")\n";
        }
    }
} catch (\Exception $e) {
    echo "Error: " . $e->getMessage() . "\n";
    echo "File: " . $e->getFile() . ":" . $e->getLine() . "\n";
}

// Test kategoris
echo "\n=== Testing kategoris() method ===\n";
try {
    $kategoris = $component->kategoris();
    echo "Kategoris count: " . $kategoris->count() . "\n";
    foreach ($kategoris as $k) {
        echo "- {$k->nama} (ID: {$k->id})\n";
    }
} catch (\Exception $e) {
    echo "Error: " . $e->getMessage() . "\n";
}

echo "\n=== Test Complete ===\n";
