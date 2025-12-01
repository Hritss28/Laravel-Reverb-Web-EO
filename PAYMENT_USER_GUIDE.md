# PANDUAN PENGGUNAAN UPDATE STATUS PEMBAYARAN

## 🎯 Fitur yang Sudah Tersedia

Sistem inventaris kantor Anda sekarang sudah memiliki fitur lengkap untuk mengupdate status pembayaran menjadi "paid" setelah pembayaran berhasil dilakukan.

## 📋 Alur Penggunaan

### 1. **User Mengajukan Peminjaman**
- User membuat peminjaman barang
- Status awal: `pending`
- Payment status: belum ada

### 2. **Admin Menyetujui Peminjaman**  
- Admin approve peminjaman di admin panel
- Status berubah menjadi: `disetujui`
- Payment status: `pending` (belum dibayar)

### 3. **User Melakukan Pembayaran**
- User melihat tombol "Bayar Sekarang" di:
  - Halaman detail peminjaman
  - Card peminjaman di daftar (quick action)
- Klik tombol → Midtrans Snap terbuka
- User input data pembayaran → selesai

### 4. **Status Otomatis Terupdate** ✅
- Payment status berubah menjadi: `paid`
- Field `paid_at` diisi dengan timestamp
- User di-redirect ke halaman detail dengan pesan sukses
- Sistem menyimpan response Midtrans

## 🎨 Visual Indikator Status

### Status Badge di UI:
- 🟢 **"✅ Sudah Dibayar"** - hijau untuk status `paid`
- 🟡 **"⏳ Menunggu Pembayaran"** - kuning untuk status `pending`  
- 🔴 **"❌ Pembayaran Gagal"** - merah untuk status `failed`

### Informasi yang Ditampilkan:
- Status pembayaran dengan ikon visual
- Total pembayaran terformat (Rp 1.000.000)
- Tanggal pembayaran (jika sudah dibayar)
- Breakdown biaya (sewa + deposit)
- Quick action buttons

## 🔧 Testing & Verifikasi

### Manual Testing:
1. Buat peminjaman baru sebagai user
2. Approve sebagai admin
3. Cek halaman detail peminjaman → ada section "Informasi Pembayaran"
4. Klik "Bayar Sekarang" → Midtrans Snap terbuka
5. Lakukan pembayaran test → status berubah menjadi "paid"

### Script Testing:
```bash
php test_payment_status.php
```

### Database Check:
```sql
SELECT kode_peminjaman, status, payment_status, paid_at 
FROM peminjamans 
WHERE payment_status = 'paid';
```

## 🛠️ Konfigurasi Midtrans (Jika Belum)

### Environment (.env):
```
MIDTRANS_SERVER_KEY=your_server_key
MIDTRANS_CLIENT_KEY=your_client_key
MIDTRANS_IS_PRODUCTION=false
```

### Midtrans Dashboard URLs:
- **Notification URL**: `https://yourdomain.com/frontend/peminjaman/payment/callback`
- **Finish URL**: `https://yourdomain.com/frontend/peminjaman/payment/finish`
- **Error URL**: `https://yourdomain.com/frontend/peminjaman/payment/error`

## 📱 User Experience

### Untuk User:
1. **Jelas melihat status pembayaran** pada setiap peminjaman
2. **Easy payment** dengan sekali klik tombol "Bayar"
3. **Real-time update** status setelah pembayaran
4. **Visual confirmation** dengan pesan sukses dan ikon

### Untuk Admin:
1. **Monitoring payment status** di admin panel
2. **Log tracking** untuk debugging
3. **Automatic workflow** tanpa manual intervention

## 🔍 Troubleshooting

### Jika Status Tidak Update:
1. **Cek log**: `storage/logs/laravel.log`
2. **Verify webhook URL** di Midtrans dashboard
3. **Test callback manually** dengan script

### Jika Error 419 (CSRF):
- Webhook route sudah di-exclude dari CSRF ✅

### Jika Error 401 (Auth):
- Webhook route tidak memerlukan auth ✅

## 📊 Monitoring & Analytics

### Log yang Tersedia:
- Payment finish callbacks
- Webhook notifications  
- Status update confirmations
- Error tracking

### Database Fields untuk Reporting:
- `payment_status`: Status pembayaran
- `paid_at`: Timestamp pembayaran
- `midtrans_response`: Data response Midtrans
- `total_pembayaran`: Jumlah yang dibayar

---

✅ **SISTEM SIAP DIGUNAKAN**
✅ Update status pembayaran berfungsi otomatis
✅ UI/UX user-friendly untuk payment workflow
✅ Monitoring dan debugging tools tersedia
