# LIVEWIRE BARANG LIST - REAL-TIME COMPONENT

## Overview
Komponen Livewire BarangList adalah implementasi real-time dari daftar barang yang terintegrasi dengan Laravel Reverb untuk menampilkan update secara langsung ketika barang baru ditambahkan dari Filament admin panel.

## Fitur Utama

### 🔄 Real-time Updates
- **Auto-notification**: Menampilkan notifikasi ketika barang baru ditambahkan
- **Live badge**: Badge "Baru!" pada barang yang baru ditambahkan  
- **Alert banner**: Banner di atas halaman untuk barang baru
- **Auto-refresh**: Opsi refresh otomatis setelah 5 detik

### 🔍 Advanced Filtering
- **Live search**: Pencarian real-time dengan debounce 300ms
- **Kategori filter**: Filter berdasarkan kategori barang
- **Kondisi filter**: Filter berdasarkan kondisi barang
- **URL sync**: Sinkronisasi filter dengan URL browser

### 📱 Modern UI/UX
- **Loading indicators**: Indikator loading saat fetch data
- **Responsive design**: Design yang responsif untuk semua device
- **Smooth animations**: Animasi smooth untuk perubahan state
- **Toast notifications**: Notifikasi toast yang elegant

## Struktur Files

### Component
```
app/Livewire/BarangList.php
```

### View
```
resources/views/livewire/barang-list.blade.php
```

### Routes
```php
// Route Livewire component
Route::get('/frontend/barang-livewire', \App\Livewire\BarangList::class)
    ->name('frontend.barang.livewire');

// Fallback route (redirect ke Livewire)
Route::get('/frontend/barang', [BarangController::class, 'index'])
    ->name('frontend.barang.index');
```

## Real-time Integration

### Event Listeners
```php
protected $listeners = [
    'echo:barang-updates,barang.created' => 'handleBarangCreated',
    'echo:barang-updates,barang.updated' => 'handleBarangUpdated', 
    'echo:barang-updates,barang.deleted' => 'handleBarangDeleted',
    'refresh-barang-list' => 'refresh',
];
```

### Broadcasting Events
Events dikirim dari `BarangObserver` ketika:
- **Created**: Barang baru ditambahkan → `barang.created`
- **Updated**: Barang diperbarui → `barang.updated`  
- **Deleted**: Barang dihapus → `barang.deleted`

### WebSocket Channel
```javascript
// Channel yang didengarkan
Echo.channel('barang-updates')
    .listen('.barang.created', (e) => { ... })
    .listen('.barang.updated', (e) => { ... })
    .listen('.barang.deleted', (e) => { ... });
```

## Component Properties

### Filter Properties
```php
public string $search = '';          // Search query
public string $kategori = '';        // Kategori filter
public string $kondisi = '';         // Kondisi filter
public int $perPage = 12;           // Items per page
```

### Real-time Properties
```php
public array $newBarangIds = [];     // IDs barang baru
public bool $showNewItemsAlert = false;  // Show new items alert
public int $newItemsCount = 0;       // Count new items
```

## Public Methods

### Filter Methods
```php
public function resetFilters()       // Reset semua filter
public function refresh()            // Refresh component data
```

### Real-time Methods
```php
public function handleBarangCreated($event)    // Handle barang baru
public function handleBarangUpdated($event)    // Handle barang update
public function handleBarangDeleted($event)    // Handle barang dihapus
public function showNewItems()                 // Tampilkan barang baru
public function dismissNewItemsAlert()         // Tutup alert barang baru
```

## JavaScript Integration

### Custom Events
```javascript
// Notifikasi real-time
$wire.on('barang-created', (event) => { ... });
$wire.on('barang-updated', (event) => { ... });
$wire.on('barang-deleted', (event) => { ... });

// URL management
$wire.on('url-updated', (params) => { ... });

// Auto-refresh countdown
$wire.on('auto-refresh-countdown', () => { ... });
```

### Notification System
```javascript
function showNotification(type, title, message) {
    // Types: success, warning, error, info
    // Auto-dismiss setelah 5 detik
    // Animasi slide-in dari kanan
}
```

## UI Components

### New Items Alert Banner
```blade
@if($showNewItemsAlert && $newItemsCount > 0)
<div class="mb-6 bg-blue-50 border border-blue-200 rounded-lg p-4">
    <!-- Alert content dengan tombol action -->
</div>
@endif
```

### Live Search & Filters
```blade
<input type="text" wire:model.live.debounce.300ms="search" />
<select wire:model.live="kategori">...</select>
<select wire:model.live="kondisi">...</select>
```

### Loading States
```blade
<div wire:loading class="mb-4">
    <!-- Loading indicator dengan spinner -->
</div>

<div wire:loading.class="opacity-50">
    <!-- Content yang di-dim saat loading -->
</div>
```

### Item Cards dengan Real-time Badges
```blade
<div class="bg-white rounded-lg {{ in_array($barang->id, $newBarangIds) ? 'ring-2 ring-blue-500 animate-pulse' : '' }}">
    @if(in_array($barang->id, $newBarangIds))
    <span class="bg-green-100 text-green-800 animate-bounce">Baru!</span>
    @endif
</div>
```

## Configuration

### Layout Integration
```php
#[Layout('frontend.layouts.app')]
class BarangList extends Component
```

### Pagination
```php
use WithPagination;

public int $perPage = 12;  // Items per page
```

### Query Optimization
```php
private function getBarangQuery()
{
    return Barang::with(['kategori'])
        ->withCount(['peminjamansAktif'])
        ->selectRaw('*, (stok - COALESCE((SELECT SUM(1) FROM peminjamans WHERE barang_id = barangs.id AND status = "dipinjam"), 0)) as stok_tersedia')
        ->orderBy('created_at', 'desc');
}
```

## Testing Real-time Functionality

### 1. Setup Services
```bash
# Start Reverb WebSocket server
php artisan reverb:start --host=127.0.0.1 --port=8080

# Start queue worker  
php artisan queue:work --timeout=60

# Build assets
npm run build
```

### 2. Test Scenarios

#### Create Barang dari Admin
1. Login ke admin panel: `/admin`
2. Navigate ke Barang → Create
3. Tambah barang baru
4. Buka tab baru ke `/frontend/barang-livewire`
5. Verifikasi:
   - ✅ Alert banner muncul
   - ✅ Badge "Baru!" di item
   - ✅ Notifikasi toast
   - ✅ Auto-refresh countdown

#### Update Barang
1. Edit barang di admin panel
2. Verifikasi di frontend:
   - ✅ Data ter-update real-time
   - ✅ Notifikasi "Barang Diperbarui"

#### Delete Barang
1. Hapus barang di admin panel
2. Verifikasi di frontend:
   - ✅ Item hilang dari list
   - ✅ Notifikasi "Barang Dihapus"

### 3. Test Manual Events
```bash
# Test via tinker
php artisan tinker
>>> broadcast(new App\Events\BarangUpdated(App\Models\Barang::first(), 'created'));
```

## Performance Considerations

### Optimization Features
- **Debounced search**: 300ms delay untuk mengurangi queries
- **Lazy loading**: Pagination untuk mengurangi memory usage
- **Efficient queries**: Optimized dengan proper relations dan counts
- **Selective refresh**: Hanya refresh data yang perlu

### Memory Management
- **Limited new items**: Maksimal tracking untuk mencegah memory leak
- **Auto-cleanup**: Array `newBarangIds` dibersihkan secara berkala
- **Pagination**: Limit data per halaman

## Security

### Access Control
- **Route middleware**: Authentication required
- **CSRF protection**: Built-in Livewire CSRF
- **XSS prevention**: Proper data escaping

### Broadcasting Security
- **Public channel**: `barang-updates` untuk semua user
- **Data filtering**: Hanya data yang aman dikirim via broadcast

## Troubleshooting

### Common Issues

#### Real-time tidak berfungsi
1. ✅ Check Reverb server running
2. ✅ Check queue worker running  
3. ✅ Check browser console untuk errors
4. ✅ Verify WebSocket connection di Network tab

#### Filter tidak bekerja
1. ✅ Check Livewire properties binding
2. ✅ Clear browser cache
3. ✅ Verify route parameters

#### Performance lambat
1. ✅ Optimize database queries
2. ✅ Reduce pagination items
3. ✅ Check database indexes

### Debug Commands
```bash
# Check routes
php artisan route:list --name=barang

# Clear cache
php artisan optimize:clear

# Monitor queue
php artisan queue:work --verbose

# Check Reverb connections
# Monitor di browser Network tab WebSocket connections
```

## Future Enhancements

### Possible Improvements
- [ ] **Infinite scroll**: Replace pagination dengan infinite scroll
- [ ] **Advanced filters**: Lebih banyak filter options
- [ ] **Bulk actions**: Multi-select untuk bulk operations
- [ ] **Favorites**: User dapat mark barang favorit
- [ ] **Compare feature**: Compare multiple barang
- [ ] **Recently viewed**: Track barang yang recently viewed

### Real-time Enhancements
- [ ] **User-specific notifications**: Notifications based pada user preferences
- [ ] **Activity feed**: Real-time activity feed
- [ ] **Stock alerts**: Alert ketika stok rendah
- [ ] **Booking conflicts**: Real-time booking conflict detection

## Integration dengan Sistem Lain

### Admin Panel Integration
- ✅ **Seamless updates**: Perubahan di admin langsung reflect di frontend
- ✅ **Shared events**: Menggunakan event system yang sama
- ✅ **Consistent data**: Data consistency across admin dan frontend

### Mobile App Ready
- ✅ **Responsive design**: Mobile-first approach
- ✅ **API compatible**: Bisa digunakan sebagai API endpoint
- ✅ **WebSocket support**: Real-time updates untuk mobile

---

## ✅ SUMMARY

Komponen Livewire BarangList memberikan:
- 🔄 **Real-time updates** dari admin panel
- 🔍 **Advanced filtering** dengan live search
- 📱 **Modern responsive UI** dengan animations
- ⚡ **High performance** dengan optimized queries
- 🔒 **Secure** dengan proper access control

**Ready untuk production dengan full real-time capabilities!** 🚀
