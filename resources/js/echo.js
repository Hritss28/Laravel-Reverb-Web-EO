import Echo from 'laravel-echo';

import Pusher from 'pusher-js';
window.Pusher = Pusher;

// Get host from environment or fallback to current hostname
const envHost = import.meta.env.VITE_REVERB_HOST || '127.0.0.1';
// Handle comma-separated hosts by taking the first one or use 127.0.0.1
const reverbHost = envHost.includes(',') ? '127.0.0.1' : envHost;
const reverbPort = import.meta.env.VITE_REVERB_PORT || 8080;
const reverbScheme = import.meta.env.VITE_REVERB_SCHEME || 'http';

console.log('Echo config:', { reverbHost, reverbPort, reverbScheme });

window.Echo = new Echo({
    broadcaster: 'reverb',
    key: import.meta.env.VITE_REVERB_APP_KEY,
    wsHost: reverbHost,
    wsPort: reverbPort,
    wssPort: reverbPort,
    forceTLS: reverbScheme === 'https',
    enabledTransports: ['ws', 'wss'],
    disableStats: true,
    encrypted: false,
});
