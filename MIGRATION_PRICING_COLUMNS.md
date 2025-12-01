# DOKUMENTASI MIGRASI KOLOM PRICING BARANG

## ✅ Status: SELESAI

Migrasi untuk menambahkan kolom harga sewa per hari dan biaya deposit ke tabel `barangs` telah berhasil dibuat dan dijalankan.

## 📋 Detail Migrasi

### File Migrasi
**Nama**: `2025_06_16_213832_add_pricing_columns_to_barangs_table.php`  
**Lokasi**: `database/migrations/`

### Kolom yang Ditambahkan

#### 1. harga_sewa_per_hari
- **Tipe**: `decimal(10,2)`
- **Default**: `0.00`
- **Posisi**: Setelah kolom `stok`
- **Keterangan**: Harga sewa barang per hari dalam rupiah
- **Nullable**: NO

#### 2. biaya_deposit
- **Tipe**: `decimal(10,2)`
- **Default**: `0.00`
- **Posisi**: Setelah kolom `harga_sewa_per_hari`
- **Keterangan**: Biaya deposit yang harus dibayar untuk peminjaman barang
- **Nullable**: NO

## 🔧 Struktur Migrasi

```php
Schema::table('barangs', function (Blueprint $table) {
    // Menambahkan kolom harga sewa per hari setelah kolom stok
    $table->decimal('harga_sewa_per_hari', 10, 2)->default(0)->after('stok')
          ->comment('Harga sewa barang per hari dalam rupiah');
    
    // Menambahkan kolom biaya deposit setelah harga sewa per hari
    $table->decimal('biaya_deposit', 10, 2)->default(0)->after('harga_sewa_per_hari')
          ->comment('Biaya deposit yang harus dibayar untuk peminjaman barang');
});
```

## 🎯 Rollback

Jika perlu melakukan rollback:
```bash
php artisan migrate:rollback --step=1
```

Migrasi memiliki method `down()` yang akan menghapus kedua kolom:
```php
$table->dropColumn(['harga_sewa_per_hari', 'biaya_deposit']);
```

## 📊 Struktur Tabel Barangs Setelah Migrasi

```
id                    - bigint unsigned (PK, auto_increment)
nama                  - varchar(255)
kode_barang          - varchar(255) (unique)
kategori_id          - bigint unsigned (FK to kategoris)
deskripsi            - text (nullable)
stok                 - int
harga_sewa_per_hari  - decimal(10,2) (default: 0.00) ✅ BARU
biaya_deposit        - decimal(10,2) (default: 0.00) ✅ BARU
kondisi              - varchar(255) (default: 'baik')
lokasi               - varchar(255)
foto                 - varchar(255) (nullable)
tersedia             - tinyint(1) (default: 1)
created_at           - timestamp (nullable)
updated_at           - timestamp (nullable)
```

## 🔗 Integrasi dengan Model

Model `Barang` sudah dikonfigurasi untuk menggunakan kolom-kolom ini:

```php
protected $fillable = [
    // ...existing fields...
    'harga_sewa_per_hari',
    'biaya_deposit',
    // ...
];

protected $casts = [
    // ...existing casts...
    'harga_sewa_per_hari' => 'decimal:2',
    'biaya_deposit' => 'decimal:2'
];
```

## 💰 Penggunaan dalam Sistem Pembayaran

Kolom-kolom ini digunakan untuk:

1. **Perhitungan Biaya Sewa**: 
   - `harga_sewa_per_hari` × `durasi` × `jumlah`

2. **Perhitungan Deposit**: 
   - `biaya_deposit` × `jumlah`

3. **Total Pembayaran**: 
   - `total_biaya_sewa` + `total_deposit`

## ✅ Verifikasi

Untuk memverifikasi bahwa kolom sudah ditambahkan:

```sql
DESCRIBE barangs;
```

Atau melalui Laravel:
```php
Schema::hasColumn('barangs', 'harga_sewa_per_hari'); // true
Schema::hasColumn('barangs', 'biaya_deposit'); // true
```

## 📝 Log Perubahan

| Tanggal | Aksi | Status |
|---------|------|--------|
| 2025-06-16 21:38 | Membuat migrasi baru | ✅ Berhasil |
| 2025-06-16 21:38 | Menjalankan migrasi | ✅ Berhasil |
| 2025-06-16 21:39 | Verifikasi kolom | ✅ Berhasil |

---

**Catatan**: Migrasi ini dibuat untuk mendokumentasikan penambahan kolom pricing yang sebelumnya ditambahkan secara manual. Sekarang semua perubahan database terdokumentasi dengan baik dalam sistem migrasi Laravel.
