import './bootstrap';

import Alpine from 'alpinejs';
import Echo from 'laravel-echo';
import Pusher from 'pusher-js';

// Setup Alpine only if not already exists
if (!window.Alpine) {
    window.Alpine = Alpine;
}

// Setup Echo only if not already exists
if (!window.Echo) {
    window.Pusher = Pusher;
    
    // Get host from environment or fallback to current hostname
    const envHost = import.meta.env.VITE_REVERB_HOST || '127.0.0.1';
    const reverbHost = envHost.includes(',') ? '127.0.0.1' : envHost;
    const reverbPort = import.meta.env.VITE_REVERB_PORT || 8080;
    const reverbScheme = import.meta.env.VITE_REVERB_SCHEME || 'http';

    window.Echo = new Echo({
        broadcaster: 'reverb',
        key: import.meta.env.VITE_REVERB_APP_KEY,
        wsHost: reverbHost,
        wsPort: reverbPort,
        wssPort: reverbPort,
        forceTLS: reverbScheme === 'https',
        enabledTransports: ['ws', 'wss'],
    });
}

// Start Alpine only once
if (!window._alpineStarted) {
    Alpine.start();
    window._alpineStarted = true;
}
