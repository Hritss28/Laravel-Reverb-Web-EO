<?php

require 'vendor/autoload.php';

use App\Models\Barang;
use App\Models\Kategori;

// Bootstrap Laravel
$app = require 'bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

try {
    echo "=== Database Connection Test ===" . PHP_EOL;
    
    echo "Total Barang: " . Barang::count() . PHP_EOL;
    echo "Total Kategori: " . Kategori::count() . PHP_EOL;
    
    echo PHP_EOL . "=== Sample Barang ===" . PHP_EOL;
    $barangs = Barang::with('kategori')->limit(5)->get();
    
    if ($barangs->isEmpty()) {
        echo "❌ Tidak ada data barang ditemukan!" . PHP_EOL;
    } else {
        foreach ($barangs as $barang) {
            echo "- " . $barang->nama . " (Kategori: " . ($barang->kategori->nama ?? 'N/A') . ")" . PHP_EOL;
        }
    }
    
    echo PHP_EOL . "=== Test Query dengan withCount ===" . PHP_EOL;
    try {
        $query = Barang::with(['kategori'])
            ->withCount(['peminjamansAktif'])
            ->selectRaw('*, (stok - COALESCE((SELECT SUM(1) FROM peminjamans WHERE barang_id = barangs.id AND status = "dipinjam"), 0)) as stok_tersedia')
            ->limit(3);
        
        echo "SQL Query: " . $query->toSql() . PHP_EOL;
        
        $results = $query->get();
        foreach ($results as $result) {
            echo "- " . $result->nama . " (Stok: " . $result->stok . ", Tersedia: " . $result->stok_tersedia . ", Aktif: " . $result->peminjamans_aktif_count . ")" . PHP_EOL;
        }
        
    } catch (Exception $e) {
        echo "❌ Error pada withCount: " . $e->getMessage() . PHP_EOL;
    }
    
    echo PHP_EOL . "=== Test Livewire Component ===" . PHP_EOL;
    try {
        $component = new \App\Livewire\BarangList();
        $component->mount();
        
        echo "✅ Livewire component mounted successfully" . PHP_EOL;
        echo "Search: '" . $component->search . "'" . PHP_EOL;
        echo "Kategori: '" . $component->kategori . "'" . PHP_EOL;
        echo "Kondisi: '" . $component->kondisi . "'" . PHP_EOL;
        
    } catch (Exception $e) {
        echo "❌ Error pada Livewire component: " . $e->getMessage() . PHP_EOL;
        echo "File: " . $e->getFile() . " Line: " . $e->getLine() . PHP_EOL;
    }
    
} catch (Exception $e) {
    echo "❌ Error: " . $e->getMessage() . PHP_EOL;
    echo "File: " . $e->getFile() . " Line: " . $e->getLine() . PHP_EOL;
}
