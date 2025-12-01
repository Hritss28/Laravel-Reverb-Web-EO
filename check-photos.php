<?php

require __DIR__ . '/vendor/autoload.php';

$app = require_once __DIR__ . '/bootstrap/app.php';
$app->make(\Illuminate\Contracts\Console\Kernel::class)->bootstrap();

use App\Models\Barang;
use Illuminate\Support\Facades\Storage;

echo "=== Checking Barang Photos ===\n\n";

$barangs = Barang::select('id', 'nama', 'foto')->take(5)->get();

foreach ($barangs as $barang) {
    echo "ID: {$barang->id}\n";
    echo "Nama: {$barang->nama}\n";
    echo "Foto path: " . ($barang->foto ?? 'NULL') . "\n";
    
    if ($barang->foto) {
        $url = Storage::url($barang->foto);
        echo "Storage URL: {$url}\n";
        
        // Check if file exists
        $exists = Storage::disk('public')->exists($barang->foto);
        echo "File exists: " . ($exists ? 'YES' : 'NO') . "\n";
        
        // Full path
        $fullPath = storage_path('app/public/' . $barang->foto);
        echo "Full path: {$fullPath}\n";
        echo "Path exists: " . (file_exists($fullPath) ? 'YES' : 'NO') . "\n";
    }
    echo "---\n";
}

// List files in storage/app/public
echo "\n=== Files in storage/app/public ===\n";
$publicPath = storage_path('app/public');
if (is_dir($publicPath)) {
    $files = scandir($publicPath);
    foreach ($files as $file) {
        if ($file != '.' && $file != '..') {
            echo "- {$file}\n";
        }
    }
} else {
    echo "Directory does not exist!\n";
}
