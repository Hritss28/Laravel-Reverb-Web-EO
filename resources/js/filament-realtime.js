// Real-time Filament Enhancement
import Echo from 'laravel-echo';

class FilamentRealtimeManager {
    constructor() {
        this.echo = window.Echo;
        this.notificationSounds = {
            success: '/sounds/success.mp3',
            warning: '/sounds/warning.mp3',
            error: '/sounds/error.mp3',
            info: '/sounds/info.mp3'
        };
        this.init();
    }

    init() {
        if (!this.echo) {
            console.warn('Laravel Echo not found. Real-time features disabled.');
            return;
        }

        this.setupGlobalListeners();
        this.setupTableListeners();
        this.setupNotificationListeners();
        this.setupConnectionStatus();
    }

    setupGlobalListeners() {
        // Listen to all public channels for real-time updates
        this.echo.channel('barang-updates')
            .listen('barang.updated', (e) => {
                this.handleBarangUpdate(e);
            });

        this.echo.channel('peminjaman-updates')
            .listen('peminjaman.updated', (e) => {
                this.handlePeminjamanUpdate(e);
            });

        this.echo.channel('kategori-updates')
            .listen('kategori.updated', (e) => {
                this.handleKategoriUpdate(e);
            });
    }

    setupTableListeners() {
        // Enhanced table real-time updates
        document.addEventListener('livewire:init', () => {
            // Auto-refresh tables when data changes
            Livewire.on('refreshTable', () => {
                this.refreshCurrentTable();
            });
        });
    }

    setupNotificationListeners() {
        // Listen to admin notifications if user is admin
        if (window.filamentData?.user?.role === 'admin') {
            this.echo.private('admin-notifications')
                .listen('admin.notification', (e) => {
                    this.showRealtimeNotification(e);
                    this.playNotificationSound(e.type);
                });
        }
    }

    setupConnectionStatus() {
        // Real-time connection status indicator
        this.createConnectionStatusIndicator();

        this.echo.connector.socket.on('connect', () => {
            this.updateConnectionStatus('connected');
        });

        this.echo.connector.socket.on('disconnect', () => {
            this.updateConnectionStatus('disconnected');
        });

        this.echo.connector.socket.on('error', () => {
            this.updateConnectionStatus('error');
        });
    }

    handleBarangUpdate(event) {
        const { action, barang } = event;
        
        // Update page title with notification
        this.updatePageTitle(`${action === 'created' ? '✅' : action === 'updated' ? '🔄' : '❌'} Barang ${action}`);
        
        // Show subtle animation for table rows
        this.animateTableUpdate('barang', barang.id, action);
        
        // Refresh relevant components
        this.refreshFilamentComponents(['barang-table', 'stats-widget']);
    }

    handlePeminjamanUpdate(event) {
        const { action, peminjaman } = event;
        
        // Special handling for status changes
        if (action === 'status_changed' || action === 'created') {
            this.showStatusChangeNotification(peminjaman);
        }
        
        // Update browser notification
        if ('Notification' in window && Notification.permission === 'granted') {
            new Notification(`Peminjaman ${action}`, {
                body: `${peminjaman.kode_peminjaman} - ${peminjaman.barang_nama}`,
                icon: '/favicon.ico',
                tag: 'peminjaman-update'
            });
        }
        
        this.refreshFilamentComponents(['peminjaman-table', 'stats-widget']);
    }

    handleKategoriUpdate(event) {
        const { action, kategori } = event;
        this.refreshFilamentComponents(['kategori-table', 'barang-table']);
    }

    showRealtimeNotification(notification) {
        // Create advanced toast notification
        const toast = this.createAdvancedToast(notification);
        document.body.appendChild(toast);
        
        // Animate in
        requestAnimationFrame(() => {
            toast.classList.add('show');
        });
        
        // Auto remove
        setTimeout(() => {
            toast.classList.add('hide');
            setTimeout(() => toast.remove(), 300);
        }, 5000);
    }

    createAdvancedToast(notification) {
        const toast = document.createElement('div');
        toast.className = `realtime-toast toast-${notification.type}`;
        
        const iconMap = {
            success: '✅',
            warning: '⚠️',
            error: '❌',
            info: 'ℹ️'
        };
        
        toast.innerHTML = `
            <div class="toast-content">
                <div class="toast-icon">${iconMap[notification.type] || 'ℹ️'}</div>
                <div class="toast-body">
                    <div class="toast-title">${notification.title}</div>
                    <div class="toast-message">${notification.message}</div>
                </div>
                <button class="toast-close" onclick="this.parentElement.parentElement.remove()">×</button>
            </div>
            <div class="toast-progress"></div>
        `;
        
        return toast;
    }

    createConnectionStatusIndicator() {
        const indicator = document.createElement('div');
        indicator.id = 'realtime-status';
        indicator.className = 'realtime-status';
        indicator.innerHTML = `
            <div class="status-dot"></div>
            <span class="status-text">Connecting...</span>
        `;
        
        // Add to Filament panel
        const panel = document.querySelector('.fi-sidebar') || document.querySelector('.fi-topbar');
        if (panel) {
            panel.appendChild(indicator);
        }
    }

    updateConnectionStatus(status) {
        const indicator = document.getElementById('realtime-status');
        if (!indicator) return;
        
        const statusConfig = {
            connected: { class: 'connected', text: 'Real-time Active', dot: '🟢' },
            disconnected: { class: 'disconnected', text: 'Disconnected', dot: '🔴' },
            error: { class: 'error', text: 'Connection Error', dot: '⚠️' }
        };
        
        const config = statusConfig[status];
        indicator.className = `realtime-status ${config.class}`;
        indicator.querySelector('.status-text').textContent = config.text;
        indicator.querySelector('.status-dot').textContent = config.dot;
    }

    refreshCurrentTable() {
        // Find and refresh current Filament table
        const tables = document.querySelectorAll('[wire\\:id]');
        tables.forEach(table => {
            if (table.getAttribute('wire:id').includes('table')) {
                const component = Livewire.find(table.getAttribute('wire:id'));
                if (component) {
                    component.call('$refresh');
                }
            }
        });
    }

    refreshFilamentComponents(componentTypes) {
        componentTypes.forEach(type => {
            const components = document.querySelectorAll(`[data-component="${type}"], [class*="${type}"]`);
            components.forEach(component => {
                if (component.hasAttribute('wire:id')) {
                    const livewireComponent = Livewire.find(component.getAttribute('wire:id'));
                    if (livewireComponent) {
                        livewireComponent.call('$refresh');
                    }
                }
            });
        });
    }

    animateTableUpdate(type, id, action) {
        // Find table row and animate
        const row = document.querySelector(`[data-${type}-id="${id}"]`);
        if (row) {
            row.classList.add(`animate-${action}`);
            setTimeout(() => row.classList.remove(`animate-${action}`), 1000);
        }
    }

    showStatusChangeNotification(peminjaman) {
        const statusColors = {
            pending: '#f59e0b',
            disetujui: '#10b981',
            ditolak: '#ef4444',
            dipinjam: '#3b82f6',
            dikembalikan: '#10b981',
            terlambat: '#ef4444'
        };
        
        this.showCustomNotification({
            title: 'Status Peminjaman Berubah',
            message: `${peminjaman.kode_peminjaman} - ${peminjaman.status}`,
            color: statusColors[peminjaman.status] || '#6b7280'
        });
    }

    showCustomNotification(options) {
        // Custom notification with better styling
        const notification = document.createElement('div');
        notification.className = 'custom-notification';
        notification.style.backgroundColor = options.color;
        notification.innerHTML = `
            <div class="notification-content">
                <strong>${options.title}</strong>
                <p>${options.message}</p>
            </div>
        `;
        
        document.body.appendChild(notification);
        
        setTimeout(() => notification.classList.add('show'), 100);
        setTimeout(() => {
            notification.classList.add('hide');
            setTimeout(() => notification.remove(), 300);
        }, 4000);
    }

    playNotificationSound(type) {
        const audio = new Audio(this.notificationSounds[type] || this.notificationSounds.info);
        audio.volume = 0.3;
        audio.play().catch(() => {
            // Ignore errors (user interaction required for audio)
        });
    }

    updatePageTitle(message) {
        const originalTitle = document.title;
        document.title = message;
        setTimeout(() => {
            document.title = originalTitle;
        }, 3000);
    }
}

// CSS for real-time enhancements
const style = document.createElement('style');
style.textContent = `
    .realtime-toast {
        position: fixed;
        top: 20px;
        right: 20px;
        z-index: 9999;
        min-width: 300px;
        background: white;
        border-radius: 12px;
        box-shadow: 0 10px 40px rgba(0,0,0,0.1);
        border-left: 4px solid;
        transform: translateX(100%);
        transition: all 0.3s ease;
        overflow: hidden;
    }
    
    .realtime-toast.show {
        transform: translateX(0);
    }
    
    .realtime-toast.hide {
        transform: translateX(100%);
        opacity: 0;
    }
    
    .toast-success { border-left-color: #10b981; }
    .toast-warning { border-left-color: #f59e0b; }
    .toast-error { border-left-color: #ef4444; }
    .toast-info { border-left-color: #3b82f6; }
    
    .toast-content {
        display: flex;
        align-items: flex-start;
        padding: 16px;
        gap: 12px;
    }
    
    .toast-icon {
        font-size: 20px;
        flex-shrink: 0;
    }
    
    .toast-body {
        flex: 1;
    }
    
    .toast-title {
        font-weight: 600;
        color: #1f2937;
        margin-bottom: 4px;
    }
    
    .toast-message {
        color: #6b7280;
        font-size: 14px;
        line-height: 1.4;
    }
    
    .toast-close {
        background: none;
        border: none;
        font-size: 20px;
        color: #9ca3af;
        cursor: pointer;
        padding: 0;
        width: 24px;
        height: 24px;
        display: flex;
        align-items: center;
        justify-content: center;
        border-radius: 4px;
        transition: colors 0.2s;
    }
    
    .toast-close:hover {
        background: #f3f4f6;
        color: #6b7280;
    }
    
    .toast-progress {
        height: 3px;
        background: linear-gradient(90deg, transparent, rgba(59,130,246,0.3), transparent);
        animation: progress 5s linear;
    }
    
    @keyframes progress {
        from { width: 100%; }
        to { width: 0%; }
    }
    
    .realtime-status {
        position: fixed;
        bottom: 20px;
        right: 20px;
        background: white;
        border: 1px solid #e5e7eb;
        border-radius: 20px;
        padding: 8px 12px;
        font-size: 12px;
        display: flex;
        align-items: center;
        gap: 6px;
        box-shadow: 0 4px 12px rgba(0,0,0,0.1);
        z-index: 1000;
    }
    
    .realtime-status.connected { border-color: #10b981; background: #f0fdf4; }
    .realtime-status.disconnected { border-color: #ef4444; background: #fef2f2; }
    .realtime-status.error { border-color: #f59e0b; background: #fffbeb; }
    
    .status-dot {
        font-size: 10px;
        animation: pulse 2s infinite;
    }
    
    @keyframes pulse {
        0%, 100% { opacity: 1; }
        50% { opacity: 0.5; }
    }
    
    .animate-created {
        animation: slideInFromRight 0.5s ease-out;
        background: #f0fdf4 !important;
    }
    
    .animate-updated {
        animation: highlightUpdate 0.5s ease-out;
    }
    
    .animate-deleted {
        animation: slideOutToRight 0.5s ease-out;
    }
    
    @keyframes slideInFromRight {
        from { transform: translateX(100%); opacity: 0; }
        to { transform: translateX(0); opacity: 1; }
    }
    
    @keyframes highlightUpdate {
        0% { background: #fef3c7; }
        100% { background: inherit; }
    }
    
    @keyframes slideOutToRight {
        from { transform: translateX(0); opacity: 1; }
        to { transform: translateX(100%); opacity: 0; }
    }
    
    .custom-notification {
        position: fixed;
        top: 20px;
        left: 50%;
        transform: translateX(-50%) translateY(-100%);
        z-index: 9999;
        color: white;
        padding: 16px 24px;
        border-radius: 8px;
        font-weight: 500;
        box-shadow: 0 8px 32px rgba(0,0,0,0.2);
        transition: transform 0.3s ease;
    }
    
    .custom-notification.show {
        transform: translateX(-50%) translateY(0);
    }
    
    .custom-notification.hide {
        transform: translateX(-50%) translateY(-100%);
    }
`;

document.head.appendChild(style);

// Initialize when DOM is loaded
document.addEventListener('DOMContentLoaded', () => {
    new FilamentRealtimeManager();
});

// Request notification permission
if ('Notification' in window && Notification.permission === 'default') {
    Notification.requestPermission();
}
