<?php

require __DIR__ . '/vendor/autoload.php';

$app = require_once __DIR__ . '/bootstrap/app.php';
$app->make(\Illuminate\Contracts\Console\Kernel::class)->bootstrap();

use App\Models\Barang;
use App\Models\Kategori;

echo "=== Testing Barang Query ===\n\n";

echo "Database: " . config('database.default') . "\n";
echo "Total Barang: " . Barang::count() . "\n";
echo "Total Kategori: " . Kategori::count() . "\n\n";

// Test simple query
echo "=== Simple Query Test ===\n";
$simple = Barang::with('kategori')->get();
echo "Simple query count: " . $simple->count() . "\n\n";

// Test the complex query
echo "=== Complex Query Test ===\n";
try {
    $query = Barang::with(['kategori'])
        ->selectRaw("barangs.*, (barangs.stok - COALESCE((SELECT COUNT(*) FROM peminjamans WHERE barang_id = barangs.id AND status = 'dipinjam'), 0)) as stok_tersedia");
    
    $result = $query->get();
    echo "Complex query count: " . $result->count() . "\n";
    
    if ($result->count() > 0) {
        echo "\nFirst item:\n";
        $first = $result->first();
        echo "- ID: " . $first->id . "\n";
        echo "- Nama: " . $first->nama . "\n";
        echo "- Stok: " . $first->stok . "\n";
        echo "- Stok Tersedia: " . $first->stok_tersedia . "\n";
        echo "- Kategori: " . ($first->kategori ? $first->kategori->nama : 'NULL') . "\n";
    }
} catch (\Exception $e) {
    echo "Error: " . $e->getMessage() . "\n";
    echo "File: " . $e->getFile() . "\n";
    echo "Line: " . $e->getLine() . "\n";
}

echo "\n=== Test Complete ===\n";
