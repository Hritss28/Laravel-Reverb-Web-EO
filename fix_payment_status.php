<?php

require_once __DIR__ . '/vendor/autoload.php';

$app = require_once __DIR__ . '/bootstrap/app.php';
$app->make(\Illuminate\Contracts\Console\Kernel::class)->bootstrap();

use App\Models\Peminjaman;

echo "=== FIXING PAYMENT STATUS MISMATCH ===\n";

// Cari peminjaman yang statusnya 'Sudah Bayar' berdasarkan screenshot
$peminjamans = Peminjaman::with(['user', 'barang'])
    ->get();

echo "Total peminjamans: " . $peminjamans->count() . "\n\n";

foreach ($peminjamans as $peminjaman) {
    echo "ID: {$peminjaman->id}\n";
    echo "Kode: {$peminjaman->kode_peminjaman}\n";
    echo "Barang: {$peminjaman->barang->nama}\n";
    echo "User: {$peminjaman->user->name}\n";
    echo "Payment Status: {$peminjaman->payment_status}\n";
    echo "Status: {$peminjaman->status}\n";
    echo "Midtrans Order ID: {$peminjaman->midtrans_order_id}\n";
    echo "Paid At: {$peminjaman->paid_at}\n";
    
    // Jika ada yang sudah dibayar tapi status masih pending
    if ($peminjaman->paid_at && $peminjaman->payment_status !== 'paid') {
        echo "❌ MISMATCH DETECTED - Has paid_at but payment_status is not 'paid'\n";
        echo "Fixing...\n";
        
        $peminjaman->update(['payment_status' => 'paid']);
        echo "✅ Fixed - Updated payment_status to 'paid'\n";
    }
    
    // Jika statusnya masih pending padahal sudah bayar
    if ($peminjaman->payment_status === 'paid' && $peminjaman->status === 'pending') {
        echo "❌ Status should be 'disetujui' since payment is completed\n";
        echo "Updating status to 'disetujui'...\n";
        
        $peminjaman->update(['status' => 'disetujui']);
        echo "✅ Fixed - Updated status to 'disetujui'\n";
    }
    
    echo "---\n";
}

// Cari secara spesifik berdasarkan info dari screenshot
echo "\n=== CHECKING SPECIFIC ITEMS FROM SCREENSHOT ===\n";

// Cari "Kulkas 2 Pintu Hemat Energi" dan "Rak Buku 5 Susun"
$kulkas = Peminjaman::whereHas('barang', function($query) {
    $query->where('nama', 'like', '%Kulkas%');
})->with(['barang', 'user'])->first();

$rakBuku = Peminjaman::whereHas('barang', function($query) {
    $query->where('nama', 'like', '%Rak Buku%');
})->with(['barang', 'user'])->first();

if ($kulkas) {
    echo "KULKAS PEMINJAMAN:\n";
    echo "ID: {$kulkas->id}\n";
    echo "Payment Status: {$kulkas->payment_status}\n";
    echo "Should be 'paid' based on screenshot\n";
    
    if ($kulkas->payment_status !== 'paid') {
        echo "Updating to 'paid'...\n";
        $kulkas->update([
            'payment_status' => 'paid',
            'paid_at' => now()
        ]);
        echo "✅ Updated Kulkas payment status\n";
    }
}

if ($rakBuku) {
    echo "\nRAK BUKU PEMINJAMAN:\n";
    echo "ID: {$rakBuku->id}\n";
    echo "Payment Status: {$rakBuku->payment_status}\n";
    echo "Should be 'paid' based on screenshot\n";
    
    if ($rakBuku->payment_status !== 'paid') {
        echo "Updating to 'paid'...\n";
        $rakBuku->update([
            'payment_status' => 'paid',
            'paid_at' => now()
        ]);
        echo "✅ Updated Rak Buku payment status\n";
    }
}

echo "\n=== DONE ===\n";
