// Real-time connection tester for Filament
window.realtimeTest = {
    connection: null,
    isConnected: false,
    
    init() {
        console.log('Initializing real-time connection test...');
        this.setupEchoListeners();
        this.testConnection();
    },
    
    setupEchoListeners() {
        if (typeof window.Echo === 'undefined') {
            console.error('Echo is not defined. Make sure echo.js is loaded.');
            return;
        }
        
        // Test public channel
        window.Echo.channel('test-channel')
            .listen('TestEvent', (e) => {
                console.log('Received test event:', e);
                this.showNotification('Test event received!', 'success');
            });
            
        // Test barang updates
        window.Echo.channel('barang-updates')
            .listen('barang.updated', (e) => {
                console.log('Barang updated:', e);
                this.showNotification('Barang updated via WebSocket!', 'info');
            })
            .listen('barang.created', (e) => {
                console.log('Barang created:', e);
                this.showNotification('New barang created!', 'success');
            });
            
        // Test peminjaman updates
        window.Echo.channel('peminjaman-updates')
            .listen('peminjaman.updated', (e) => {
                console.log('Peminjaman updated:', e);
                this.showNotification('Peminjaman updated via WebSocket!', 'info');
            })
            .listen('peminjaman.created', (e) => {
                console.log('Peminjaman created:', e);
                this.showNotification('New peminjaman created!', 'success');
            })
            .listen('peminjaman.status_changed', (e) => {
                console.log('Peminjaman status changed:', e);
                this.showNotification('Peminjaman status changed!', 'warning');
            });
    },
    
    testConnection() {
        console.log('Testing WebSocket connection...');
        
        // Test basic connection
        if (window.Echo && window.Echo.connector) {
            const connector = window.Echo.connector;
            
            connector.pusher.connection.bind('connected', () => {
                console.log('✅ WebSocket connected successfully!');
                this.isConnected = true;
                this.showNotification('WebSocket connected!', 'success');
                this.updateConnectionStatus(true);
            });
            
            connector.pusher.connection.bind('disconnected', () => {
                console.log('❌ WebSocket disconnected!');
                this.isConnected = false;
                this.showNotification('WebSocket disconnected!', 'error');
                this.updateConnectionStatus(false);
            });
            
            connector.pusher.connection.bind('error', (error) => {
                console.error('WebSocket error:', error);
                this.showNotification('WebSocket error: ' + error.error.message, 'error');
                this.updateConnectionStatus(false);
            });
            
            connector.pusher.connection.bind('unavailable', () => {
                console.warn('WebSocket unavailable');
                this.showNotification('WebSocket unavailable', 'warning');
                this.updateConnectionStatus(false);
            });
        }
    },
    
    updateConnectionStatus(connected) {
        // Update connection status in dashboard
        const statusElements = document.querySelectorAll('[data-connection-status]');
        statusElements.forEach(el => {
            el.textContent = connected ? 'Connected' : 'Disconnected';
            el.className = connected ? 'text-green-600' : 'text-red-600';
        });
        
        // Dispatch Livewire event
        if (window.Livewire) {
            window.Livewire.dispatch('connection-status-changed', { connected });
        }
    },
    
    showNotification(message, type = 'info') {
        console.log(`[${type.toUpperCase()}] ${message}`);
        
        // Create notification element
        const notification = document.createElement('div');
        notification.className = `fixed top-4 right-4 p-4 rounded-lg shadow-lg z-50 ${this.getNotificationClass(type)}`;
        notification.innerHTML = `
            <div class="flex items-center">
                <span class="mr-2">${this.getNotificationIcon(type)}</span>
                <span>${message}</span>
                <button onclick="this.parentElement.parentElement.remove()" class="ml-4 text-lg">&times;</button>
            </div>
        `;
        
        document.body.appendChild(notification);
        
        // Auto remove after 5 seconds
        setTimeout(() => {
            if (notification.parentElement) {
                notification.remove();
            }
        }, 5000);
    },
    
    getNotificationClass(type) {
        switch(type) {
            case 'success': return 'bg-green-500 text-white';
            case 'error': return 'bg-red-500 text-white';
            case 'warning': return 'bg-yellow-500 text-black';
            case 'info': return 'bg-blue-500 text-white';
            default: return 'bg-gray-500 text-white';
        }
    },
    
    getNotificationIcon(type) {
        switch(type) {
            case 'success': return '✅';
            case 'error': return '❌';
            case 'warning': return '⚠️';
            case 'info': return 'ℹ️';
            default: return '📢';
        }
    },
    
    triggerTestEvent() {
        console.log('Triggering test event...');
        fetch('/test-broadcast', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
            },
            body: JSON.stringify({ message: 'Test broadcast from frontend' })
        })
        .then(response => response.json())
        .then(data => {
            console.log('Test event triggered:', data);
            this.showNotification('Test event sent!', 'info');
        })
        .catch(error => {
            console.error('Error triggering test event:', error);
            this.showNotification('Error sending test event', 'error');
        });
    }
};

// Auto-initialize when DOM is ready
document.addEventListener('DOMContentLoaded', function() {
    setTimeout(() => {
        window.realtimeTest.init();
    }, 1000);
});

// Export for manual testing
window.testRealtime = window.realtimeTest;
