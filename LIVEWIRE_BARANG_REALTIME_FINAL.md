# Livewire Barang Real-time Implementation

## Overview
Komponen Livewire `BarangList` telah diintegrasikan dengan Laravel Reverb untuk menyediakan update real-time ketika admin menambah, mengubah, atau menghapus barang dari Filament admin panel.

## Features Implemented

### 1. Real-time Barang List
- **Component**: `App\Livewire\BarangList`
- **View**: `resources/views/livewire/barang-list.blade.php`
- **Route**: `/frontend/barang-livewire`

### 2. Real-time Event Broadcasting
- **Observer**: `App\Observers\BarangObserver`
- **Event**: `App\Events\BarangUpdated`
- **Channels**: 
  - `barang-updates` (public channel)
  - `admin-notifications` (private channel)

### 3. Event Types Broadcasted
- `barang.created` - When new barang is created
- `barang.updated` - When barang is updated
- `barang.deleted` - When barang is deleted

### 4. Features dalam Livewire Component

#### Filtering & Search
- Live search (debounced 300ms)
- Filter by kategori
- Filter by kondisi
- Real-time URL updates

#### Real-time Notifications
- Toast notifications for new/updated/deleted barang
- Visual badge for new items alert
- Auto-refresh option
- Sound/visual feedback

#### Advanced UI Features
- Loading indicators
- Pagination
- Responsive grid layout
- Real-time stock updates
- Price formatting
- Status badges

## File Structure

```
app/
├── Events/
│   ├── BarangUpdated.php        # Broadcast event
│   └── AdminNotification.php    # Admin notifications
├── Livewire/
│   └── BarangList.php          # Main real-time component
├── Observers/
│   └── BarangObserver.php      # Model observer for broadcasting
└── Http/Controllers/Frontend/
    └── BarangController.php    # Redirects to Livewire

resources/
├── views/
│   ├── livewire/
│   │   └── barang-list.blade.php     # Livewire view
│   └── frontend/
│       ├── layouts/
│       │   └── app.blade.php         # Updated with Echo JS
│       └── barang/
│           └── index.blade.php       # Migration notice
└── js/
    ├── app.js                        # Main JS
    └── echo.js                       # Echo configuration

routes/
└── web.php                          # Routes configuration
```

## Configuration

### 1. Broadcasting Configuration
```php
// config/broadcasting.php
'reverb' => [
    'driver' => 'reverb',
    'key' => env('REVERB_APP_KEY'),
    'secret' => env('REVERB_APP_SECRET'),
    'app_id' => env('REVERB_APP_ID'),
    'options' => [
        'host' => env('REVERB_HOST', '127.0.0.1'),
        'port' => env('REVERB_PORT', 8080),
        'scheme' => env('REVERB_SCHEME', 'http'),
    ],
],
```

### 2. Echo Configuration
```javascript
// resources/js/echo.js
window.Echo = new Echo({
    broadcaster: 'reverb',
    key: import.meta.env.VITE_REVERB_APP_KEY,
    wsHost: import.meta.env.VITE_REVERB_HOST,
    wsPort: import.meta.env.VITE_REVERB_PORT ?? 80,
    wssPort: import.meta.env.VITE_REVERB_PORT ?? 443,
    forceTLS: (import.meta.env.VITE_REVERB_SCHEME ?? 'http') === 'https',
    enabledTransports: ['ws', 'wss'],
});
```

## Usage

### 1. Starting Services
```bash
# Start Reverb server
php artisan reverb:start --host=0.0.0.0 --port=8080

# Start queue worker
php artisan queue:work

# Build assets
npm run build
```

### 2. Accessing Real-time Barang List
- **Direct URL**: `/frontend/barang-livewire`
- **Redirect**: `/frontend/barang` (automatically redirects)

### 3. Creating Test Data
```bash
php test-realtime-barang.php
```

## Real-time Events Handling

### Component Event Listeners
```php
protected $listeners = [
    'echo:barang-updates,barang.created' => 'handleBarangCreated',
    'echo:barang-updates,barang.updated' => 'handleBarangUpdated',
    'echo:barang-updates,barang.deleted' => 'handleBarangDeleted',
    'refresh-barang-list' => 'refresh',
];
```

### JavaScript Notifications
- Toast notifications appear in top-right corner
- Auto-dismiss after 5 seconds
- Manual dismiss option
- Different styles for create/update/delete events

## Testing

### 1. Manual Testing
1. Open frontend barang list in browser
2. Open Filament admin panel in another tab
3. Create/update/delete barang from admin
4. Observe real-time updates on frontend

### 2. Automated Testing
```bash
# Run test script
php test-realtime-barang.php

# Check queue jobs
php artisan queue:failed

# Monitor Reverb connections
# Check browser console for Echo connection status
```

## Troubleshooting

### 1. Events Not Broadcasting
- Ensure Reverb server is running
- Check queue worker is processing jobs
- Verify `.env` configuration for Reverb
- Check browser console for JavaScript errors

### 2. Frontend Not Updating
- Ensure Echo JS is loaded properly
- Check WebSocket connection in browser DevTools
- Verify Livewire component is listening to correct events
- Check that channel names match between backend and frontend

### 3. Performance Issues
- Monitor queue job processing time
- Check database queries in Livewire component
- Consider implementing event throttling for high-volume updates

## Environment Variables Required

```env
# Broadcasting
BROADCAST_DRIVER=reverb

# Reverb Configuration
REVERB_APP_ID=your-app-id
REVERB_APP_KEY=your-app-key
REVERB_APP_SECRET=your-app-secret
REVERB_HOST=127.0.0.1
REVERB_PORT=8080
REVERB_SCHEME=http

# Vite Configuration
VITE_REVERB_APP_KEY="${REVERB_APP_KEY}"
VITE_REVERB_HOST="${REVERB_HOST}"
VITE_REVERB_PORT="${REVERB_PORT}"
VITE_REVERB_SCHEME="${REVERB_SCHEME}"
```

## Security Considerations

1. **Public Channel**: `barang-updates` is a public channel - no authentication required
2. **Data Filtering**: Only necessary data is broadcast (no sensitive information)
3. **Rate Limiting**: Consider implementing rate limiting for high-volume events
4. **Validation**: All data is validated before broadcasting

## Performance Optimizations

1. **Debounced Search**: 300ms debounce to prevent excessive queries
2. **Pagination**: Default 12 items per page
3. **Lazy Loading**: Images and data loaded as needed
4. **Selective Updates**: Only affected data is refreshed
5. **Connection Management**: Proper Echo connection handling

## Future Enhancements

1. **Real-time Stock Counters**: Live stock updates during peminjaman
2. **User Presence**: Show who's viewing the same items
3. **Advanced Filters**: More granular filtering options
4. **Infinite Scroll**: For better mobile experience
5. **Offline Support**: Cache updates when connection is lost

## Migration from Blade to Livewire

The old Blade view (`resources/views/frontend/barang/index.blade.php`) now shows a migration notice and automatically redirects users to the new Livewire component for better real-time experience.

Routes have been updated to ensure seamless migration:
- `frontend.barang.index` → redirects to → `frontend.barang.livewire`
- Navigation menus updated to point directly to Livewire component
- URL parameters are preserved during migration
