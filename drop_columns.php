<?php

require_once 'vendor/autoload.php';

// Load Laravel
$app = require_once 'bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;

try {
    echo "Menghapus kolom harga_sewa_per_hari dan biaya_deposit dari tabel barangs...\n";
    
    Schema::table('barangs', function (Blueprint $table) {
        $table->dropColumn(['harga_sewa_per_hari', 'biaya_deposit']);
    });
    
    echo "✅ Kolom berhasil dihapus!\n";
} catch (Exception $e) {
    echo "❌ Error: " . $e->getMessage() . "\n";
}
