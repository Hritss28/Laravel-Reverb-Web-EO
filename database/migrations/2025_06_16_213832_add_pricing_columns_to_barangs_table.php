<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('barangs', function (Blueprint $table) {
            // Menambahkan kolom harga sewa per hari setelah kolom stok
            $table->decimal('harga_sewa_per_hari', 10, 2)->default(0)->after('stok')
                  ->comment('Harga sewa barang per hari dalam rupiah');
            
            // Menambahkan kolom biaya deposit setelah harga sewa per hari
            $table->decimal('biaya_deposit', 10, 2)->default(0)->after('harga_sewa_per_hari')
                  ->comment('Biaya deposit yang harus dibayar untuk peminjaman barang');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('barangs', function (Blueprint $table) {
            $table->dropColumn(['harga_sewa_per_hari', 'biaya_deposit']);
        });
    }
};
