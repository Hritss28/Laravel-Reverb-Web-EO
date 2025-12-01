# Livewire Realtime Dashboard

## Overview
The RealtimeDashboard has been refactored from a Filament Page to a standalone Livewire component for better flexibility and real-time performance.

## Architecture

### Files Structure
- **Component**: `app/Livewire/RealtimeDashboard.php`
- **View**: `resources/views/livewire/realtime-dashboard.blade.php`  
- **Layout**: `resources/views/layouts/livewire-app.blade.php`
- **Route**: `/realtime-dashboard` (auth required)

### Key Features

1. **Real-time Statistics Monitoring**
   - Total Barang
   - Stok Rendah
   - Peminjaman Pending
   - Peminjaman Aktif
   - Terlambat
   - Total Users
   - Peminjaman Hari Ini
   - Connection Status

2. **Live Activity Feed**
   - Barang events (created, updated, deleted)
   - Peminjaman events (created, updated, deleted, status changes)
   - User events
   - Kategori events
   - System events (connection status)
   - Test events

3. **WebSocket Integration**
   - Laravel Reverb WebSocket server
   - Real-time event broadcasting
   - Connection status monitoring
   - Auto-reconnection handling

4. **Interactive Features**
   - Manual data refresh
   - Test connection functionality
   - Activity log clearing
   - Auto-refresh toggle
   - Real-time notifications

## Technical Implementation

### Event Listeners
The component listens to various broadcasting channels:
```php
protected $listeners = [
    'echo:barang-updates,barang.updated' => 'handleBarangUpdate',
    'echo:barang-updates,barang.created' => 'handleBarangCreate',
    'echo:barang-updates,barang.deleted' => 'handleBarangDelete',
    'echo:peminjaman-updates,peminjaman.updated' => 'handlePeminjamanUpdate',
    'echo:peminjaman-updates,peminjaman.created' => 'handlePeminjamanCreate',
    'echo:peminjaman-updates,peminjaman.deleted' => 'handlePeminjamanDelete',
    'echo:peminjaman-updates,peminjaman.status_changed' => 'handlePeminjamanStatusChange',
    'echo:user-updates,user.updated' => 'handleUserUpdate',
    'echo:kategori-updates,kategori.updated' => 'handleKategoriUpdate',
    'echo:test-channel,TestEvent' => 'handleTestEvent',
    'reverb-connected' => 'handleReverbConnected',
    'reverb-disconnected' => 'handleReverbDisconnected',
];
```

### Model Observers
The system uses Model Observers to automatically broadcast events:
- `BarangObserver` - broadcasts barang changes
- `PeminjamanObserver` - broadcasts peminjaman changes
- `UserObserver` - broadcasts user changes
- `KategoriObserver` - broadcasts kategori changes

### JavaScript Integration
- Uses Laravel Echo for WebSocket communication
- Custom notifications system using `@script` directive
- Real-time UI updates without page refresh
- Connection status monitoring

## Setup & Configuration

### Prerequisites
1. Laravel Reverb installed and configured
2. Queue worker running
3. WebSocket server (Reverb) running
4. Models with appropriate Observers registered

### Starting Services
```bash
# Start Reverb WebSocket server
php artisan reverb:start --host=127.0.0.1 --port=8080

# Start queue worker
php artisan queue:work --timeout=60

# Build assets
npm run build
```

### Environment Configuration
```env
BROADCAST_DRIVER=reverb
REVERB_APP_ID=your-app-id
REVERB_APP_KEY=your-app-key
REVERB_APP_SECRET=your-app-secret
REVERB_HOST="127.0.0.1"
REVERB_PORT=8080
REVERB_SCHEME=http

VITE_REVERB_APP_KEY="${REVERB_APP_KEY}"
VITE_REVERB_HOST="${REVERB_HOST}"
VITE_REVERB_PORT="${REVERB_PORT}"
VITE_REVERB_SCHEME="${REVERB_SCHEME}"
```

## Usage

### Accessing the Dashboard
1. **Direct URL**: `/realtime-dashboard` (requires authentication)
2. **From Admin Panel**: Click "Real-time Dashboard" button on main dashboard

### Testing Real-time Functionality
1. Use the "Test Connection" button in the dashboard
2. Create/update records in Admin Panel
3. Use test routes:
   - `POST /test-broadcast` - Send test broadcast
   - `GET /test-peminjaman-update` - Trigger peminjaman update

### API Methods

#### Public Methods
- `refreshData()` - Manual data refresh
- `testConnection()` - Send test broadcast event
- `clearActivity()` - Clear activity log
- `loadRealtimeStats()` - Load current statistics
- `loadRecentActivity()` - Load recent activity

#### Event Handlers
- `handleBarangUpdate($event)` - Handle barang update events
- `handleBarangCreate($event)` - Handle barang creation events
- `handlePeminjamanCreate($event)` - Handle peminjaman creation events
- `handlePeminjamanStatusChange($event)` - Handle status changes
- `handleTestEvent($event)` - Handle test events
- `handleReverbConnected()` - Handle connection established
- `handleReverbDisconnected()` - Handle connection lost

## UI Components

### Statistics Cards
- Real-time updating numeric displays
- Color-coded based on status/priority
- Responsive grid layout

### Activity Feed
- Chronological list of events
- Type-specific icons and colors
- Relative timestamps
- Auto-scrolling with limit (15 items)

### System Information Panel
- Connection status indicator
- Auto-refresh toggle
- Action buttons
- Link to admin panel

### Notifications
- Custom JavaScript-based notifications
- Auto-dismiss after 5 seconds
- Type-specific styling (success, warning, error, info)
- Manual close option

## Customization

### Adding New Event Types
1. Create new Model Observer
2. Register observer in `AppServiceProvider`
3. Add event handler method in component
4. Add listener to `$listeners` array
5. Update UI icons/colors in view

### Styling Modifications
- Edit `resources/views/livewire/realtime-dashboard.blade.php`
- Uses Tailwind CSS classes
- Responsive design built-in
- Dark mode support available

### Layout Customization
- Modify `resources/views/layouts/livewire-app.blade.php`
- Add/remove navigation items
- Change branding/styling
- Include additional assets

## Troubleshooting

### Common Issues
1. **Events not received**: Check Reverb server and queue worker status
2. **Connection failed**: Verify WebSocket configuration in `.env`
3. **Assets not loading**: Run `npm run build` after changes
4. **404 on dashboard**: Clear route cache with `php artisan route:clear`

### Debugging
- Check browser console for JavaScript errors
- Monitor queue worker output for failed jobs
- Verify database events are triggering observers
- Test WebSocket connection manually

### Performance Optimization
- Limit activity feed to prevent memory issues
- Use database indexing for statistics queries
- Consider Redis for session storage
- Monitor WebSocket connection limits

## Integration with Filament

### Admin Panel Integration
- Header action on main dashboard links to standalone version
- Maintains separate navigation/functionality
- Shared authentication system
- Compatible with Filament notifications

### Future Enhancements
- Integration with Filament Tables for live updates
- Custom Filament widgets using the same real-time data
- Admin-configurable dashboard layout
- Real-time chat integration
- Performance metrics and monitoring

## Security Considerations

### Access Control
- Authentication required for dashboard access
- Broadcasting security via channels authorization
- CSRF protection on all actions
- Rate limiting on WebSocket connections

### Data Protection
- No sensitive data in WebSocket messages
- Activity logs automatically limited
- User-specific data filtering
- Secure WebSocket configuration
