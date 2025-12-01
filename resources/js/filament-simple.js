// Simple Filament Real-time Integration with Livewire
console.log('🚀 Loading Filament Real-time...');

document.addEventListener('DOMContentLoaded', function() {
    // Wait for Echo to be available
    const waitForEcho = setInterval(() => {
        if (typeof window.Echo !== 'undefined') {
            clearInterval(waitForEcho);
            initializeRealtimeListeners();
        }
    }, 100);
    
    function initializeRealtimeListeners() {
        console.log('✅ Echo found, setting up Filament listeners...');
        
        // Connection status listeners
        if (window.Echo.connector && window.Echo.connector.pusher) {
            window.Echo.connector.pusher.connection.bind('connected', () => {
                console.log('🟢 Reverb connected');
                triggerLivewireEvent('reverb-connected');
                showConnectionStatus('Connected', 'success');
            });
            
            window.Echo.connector.pusher.connection.bind('disconnected', () => {
                console.log('🔴 Reverb disconnected');
                triggerLivewireEvent('reverb-disconnected');
                showConnectionStatus('Disconnected', 'error');
            });
            
            window.Echo.connector.pusher.connection.bind('error', (error) => {
                console.error('❌ Reverb error:', error);
                showConnectionStatus('Error: ' + error.error?.message, 'error');
            });
        }
        
        // Listen for Peminjaman updates
        window.Echo.channel('peminjaman-updates')
            .listen('peminjaman.updated', (event) => {
                console.log('📝 Peminjaman updated:', event);
                triggerLivewireEvent('handlePeminjamanUpdate', event);
                refreshFilamentComponents();
                showUpdateNotification('Peminjaman diperbarui');
            })
            .listen('peminjaman.created', (event) => {
                console.log('➕ Peminjaman created:', event);
                triggerLivewireEvent('handlePeminjamanCreate', event);
                refreshFilamentComponents();
                showUpdateNotification('Peminjaman baru dibuat', 'success');
            })
            .listen('peminjaman.status_changed', (event) => {
                console.log('🔄 Status changed:', event);
                triggerLivewireEvent('handlePeminjamanStatusChange', event);
                refreshFilamentComponents();
                showUpdateNotification('Status peminjaman berubah', 'warning');
            })
            .listen('peminjaman.deleted', (event) => {
                console.log('🗑️ Peminjaman deleted:', event);
                triggerLivewireEvent('handlePeminjamanDelete', event);
                refreshFilamentComponents();
                showUpdateNotification('Peminjaman dihapus', 'error');
            });
        
        // Listen for Barang updates
        window.Echo.channel('barang-updates')
            .listen('barang.updated', (event) => {
                console.log('📦 Barang updated:', event);
                triggerLivewireEvent('handleBarangUpdate', event);
                refreshFilamentComponents();
                showUpdateNotification('Barang diperbarui');
            })
            .listen('barang.created', (event) => {
                console.log('📦 Barang created:', event);
                triggerLivewireEvent('handleBarangCreate', event);
                refreshFilamentComponents();
                showUpdateNotification('Barang baru ditambahkan', 'success');
            })
            .listen('barang.deleted', (event) => {
                console.log('📦 Barang deleted:', event);
                triggerLivewireEvent('handleBarangDelete', event);
                refreshFilamentComponents();
                showUpdateNotification('Barang dihapus', 'error');
            });
        
        // Listen for User updates
        window.Echo.channel('user-updates')
            .listen('user.updated', (event) => {
                console.log('👤 User updated:', event);
                triggerLivewireEvent('handleUserUpdate', event);
                refreshFilamentComponents();
                showUpdateNotification('Data user diperbarui');
            });
        
        // Listen for Kategori updates
        window.Echo.channel('kategori-updates')
            .listen('kategori.updated', (event) => {
                console.log('🏷️ Kategori updated:', event);
                triggerLivewireEvent('handleKategoriUpdate', event);
                refreshFilamentComponents();
                showUpdateNotification('Kategori diperbarui');
            });
            
        // Listen for Test events
        window.Echo.channel('test-channel')
            .listen('TestEvent', (event) => {
                console.log('🧪 Test event received:', event);
                triggerLivewireEvent('handleTestEvent', event);
                showUpdateNotification('Test event received!', 'info');
            });
            
        console.log('✅ Filament real-time listeners active!');
    }
    
    function triggerLivewireEvent(eventName, data = {}) {
        if (typeof window.Livewire !== 'undefined') {
            try {
                // Try to dispatch to specific component first
                const dashboardComponent = document.querySelector('[wire\\:id*="realtime-dashboard"]');
                if (dashboardComponent && dashboardComponent.__livewire) {
                    dashboardComponent.__livewire.call(eventName, data);
                    console.log(`🎯 Livewire event '${eventName}' sent to dashboard component`);
                } else {
                    // Fallback to global dispatch
                    window.Livewire.dispatch(eventName, data);
                    console.log(`📡 Livewire event '${eventName}' dispatched globally`);
                }
            } catch (error) {
                console.error(`❌ Error triggering Livewire event '${eventName}':`, error);
            }
        }
    }
    
    function refreshFilamentComponents() {
        // Method 1: Livewire global refresh
        if (typeof window.Livewire !== 'undefined') {
            try {
                // Dispatch refresh to all components
                window.Livewire.dispatch('$refresh');
                console.log('🔄 Livewire refresh dispatched');
            } catch (error) {
                console.error('❌ Livewire refresh error:', error);
            }
        }
        
        // Method 2: Force reload specific table elements
        setTimeout(() => {
            const tableElements = document.querySelectorAll('[wire\\:key*="table"]');
            tableElements.forEach(element => {
                if (element.__livewire) {
                    element.__livewire.call('$refresh');
                }
            });
        }, 100);
    }
    
    function showConnectionStatus(message, type = 'info') {
        const statusBadge = document.querySelector('[data-connection-status]');
        if (statusBadge) {
            statusBadge.textContent = message;
            statusBadge.className = getStatusClass(type);
        }
    }
    
    function getStatusClass(type) {
        const baseClass = 'inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium';
        switch(type) {
            case 'success': return `${baseClass} bg-green-100 text-green-800`;
            case 'error': return `${baseClass} bg-red-100 text-red-800`;
            case 'warning': return `${baseClass} bg-yellow-100 text-yellow-800`;
            default: return `${baseClass} bg-blue-100 text-blue-800`;
        }
    }
    
    function showUpdateNotification(message, type = 'info') {
        console.log(`[${type.toUpperCase()}] ${message}`);
        
        // Create notification element
        const notification = document.createElement('div');
        notification.style.cssText = `
            position: fixed;
            top: 20px;
            right: 20px;
            background: ${getNotificationColor(type)};
            color: white;
            padding: 12px 16px;
            border-radius: 8px;
            box-shadow: 0 4px 12px rgba(0,0,0,0.15);
            z-index: 9999;
            font-size: 14px;
            transition: all 0.3s ease;
            max-width: 300px;
        `;
        notification.innerHTML = `
            <div class="flex items-center">
                <span class="mr-2">${getNotificationIcon(type)}</span>
                <span>${message}</span>
                <button onclick="this.parentElement.parentElement.remove()" 
                        class="ml-4 text-lg leading-none opacity-70 hover:opacity-100">&times;</button>
            </div>
        `;
        
        document.body.appendChild(notification);
        
        // Auto remove after 4 seconds
        setTimeout(() => {
            if (notification.parentElement) {
                notification.style.opacity = '0';
                notification.style.transform = 'translateX(100%)';
                setTimeout(() => notification.remove(), 300);
            }
        }, 4000);
    }
    
    function getNotificationColor(type) {
        switch(type) {
            case 'success': return '#10b981';
            case 'error': return '#ef4444';
            case 'warning': return '#f59e0b';
            case 'info': return '#3b82f6';
            default: return '#6b7280';
        }
    }
    
    function getNotificationIcon(type) {
        switch(type) {
            case 'success': return '✅';
            case 'error': return '❌';
            case 'warning': return '⚠️';
            case 'info': return 'ℹ️';
            default: return '📢';
        }
    }
    
    // Start initialization
    initEcho();
});

// Global functions for manual testing
window.forceRefreshFilament = function() {
    if (typeof window.Livewire !== 'undefined') {
        window.Livewire.dispatch('$refresh');
        console.log('🔄 Manual Filament refresh');
    }
};

window.testRealtimeConnection = function() {
    fetch('/test-broadcast', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.getAttribute('content')
        }
    })
    .then(response => response.json())
    .then(data => console.log('✅ Test broadcast sent:', data))
    .catch(error => console.error('❌ Test failed:', error));
};
