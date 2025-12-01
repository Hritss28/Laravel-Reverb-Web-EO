// Real-time Connection Monitor
// Add this to browser console to monitor Echo connections

console.log('🔍 Starting Echo Connection Monitor...');

// Monitor Echo connection status
if (window.Echo) {
    console.log('✅ Echo is available');
    
    // Listen to barang updates
    window.Echo.channel('barang-updates')
        .listen('.barang.created', (e) => {
            console.log('🆕 Barang Created:', e);
        })
        .listen('.barang.updated', (e) => {
            console.log('📝 Barang Updated:', e);
        })
        .listen('.barang.deleted', (e) => {
            console.log('🗑️ Barang Deleted:', e);
        });
    
    console.log('👂 Listening to barang-updates channel...');
    
    // Test connection
    setTimeout(() => {
        console.log('🔔 Testing connection...');
        window.Echo.channel('barang-updates').whisper('test', {
            message: 'Connection test from browser console'
        });
    }, 2000);
    
} else {
    console.log('❌ Echo is not available');
    console.log('💡 Make sure Vite assets are built and loaded properly');
}

// Monitor WebSocket connection
if (window.Echo && window.Echo.connector && window.Echo.connector.socket) {
    const socket = window.Echo.connector.socket;
    
    socket.connection.bind('connected', () => {
        console.log('🔗 WebSocket Connected');
    });
    
    socket.connection.bind('disconnected', () => {
        console.log('🔌 WebSocket Disconnected');
    });
    
    socket.connection.bind('error', (error) => {
        console.log('❌ WebSocket Error:', error);
    });
    
    console.log('📊 Current Connection State:', socket.connection.state);
}

// Helper function to manually trigger test events
window.testBarangCreated = function() {
    if (window.Echo) {
        console.log('🧪 Triggering test barang.created event...');
        window.Echo.channel('barang-updates').listen('.barang.created', (e) => {
            console.log('✅ Test event received:', e);
        });
        
        // Simulate event (for testing frontend only)
        window.Livewire.first().call('handleBarangCreated', {
            data: {
                id: 999,
                nama: 'Test Item from Console',
                kode_barang: 'TEST-CONSOLE'
            }
        });
    }
};

console.log('📋 Available commands:');
console.log('  - testBarangCreated() : Test frontend event handling');
console.log('  - window.Echo.channel("barang-updates") : Access channel directly');
console.log('  - window.Livewire.first() : Access Livewire component');
