<?php

require 'vendor/autoload.php';

use App\Models\Barang;
use App\Models\Kategori;

// Bootstrap Laravel
$app = require 'bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

try {
    // Create test kategori if not exists
    $kategori = Kategori::firstOrCreate(
        ['nama' => 'Test Kategori'],
        [
            'nama' => 'Test Kategori',
            'kode' => 'TEST-KAT-' . rand(100, 999),
            'deskripsi' => 'Kategori untuk test real-time'
        ]
    );
    
    // Create test barang
    $barang = Barang::create([
        'nama' => 'Test Barang Real-time ' . now()->format('H:i:s'),
        'kode_barang' => 'TEST-' . rand(1000, 9999),
        'kategori_id' => $kategori->id,
        'deskripsi' => 'Test barang untuk real-time update',
        'stok' => 5,
        'kondisi' => 'baik',
        'lokasi' => 'Gudang Test',
        'tersedia' => true,
        'harga_sewa_per_hari' => 10000,
        'biaya_deposit' => 20000
    ]);
    
    echo "✅ Test barang berhasil dibuat!" . PHP_EOL;
    echo "📦 Nama: " . $barang->nama . PHP_EOL;
    echo "🔢 ID: " . $barang->id . PHP_EOL;
    echo "📋 Kode: " . $barang->kode_barang . PHP_EOL;
    echo "⏰ Waktu: " . $barang->created_at . PHP_EOL;
    echo PHP_EOL;
    echo "🔴 Event 'barang.created' seharusnya telah di-broadcast ke channel 'barang-updates'" . PHP_EOL;
    echo "🌐 Cek frontend untuk melihat notifikasi real-time!" . PHP_EOL;
    
} catch (Exception $e) {
    echo "❌ Error: " . $e->getMessage() . PHP_EOL;
}
