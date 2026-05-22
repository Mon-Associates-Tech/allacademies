import Echo from 'laravel-echo';
import Pusher from 'pusher-js';

window.Pusher = Pusher;

// DEBUG: Log all environment variables
console.log('=== ECHO.JS LOADED ===');
console.log('VITE_REVERB_APP_KEY:', import.meta.env.VITE_REVERB_APP_KEY);
console.log('VITE_REVERB_HOST:', import.meta.env.VITE_REVERB_HOST);
console.log('VITE_REVERB_PORT:', import.meta.env.VITE_REVERB_PORT);
console.log('VITE_REVERB_SCHEME:', import.meta.env.VITE_REVERB_SCHEME);

// Check if values exist
const key = import.meta.env.VITE_REVERB_APP_KEY;
const host = import.meta.env.VITE_REVERB_HOST;
const port = import.meta.env.VITE_REVERB_PORT;
const scheme = import.meta.env.VITE_REVERB_SCHEME;

if (!key) {
    console.error('❌ VITE_REVERB_APP_KEY is missing!');
}

console.log('Creating Echo instance with:', {
    key,
    wsHost: host,
    wsPort: port,
    forceTLS: scheme === 'https',
});

try {
    window.Echo = new Echo({
        broadcaster: 'reverb',
        key: key,
        wsHost: host,
        wsPort: parseInt(port) || 8080,
        wssPort: parseInt(port) || 8080,
        forceTLS: false, // Force HTTP instead of HTTPS
        encrypted: false, // Don't use encryption for local development
        disableStats: true,
        enabledTransports: ['ws'], // Only use ws, not wss
    });

    console.log('✅ Echo instance created:', window.Echo);
    console.log('Pusher instance:', window.Echo.connector?.pusher);

    // Try to bind connection events
    if (window.Echo.connector?.pusher?.connection) {
        console.log('Binding connection events...');

        window.Echo.connector.pusher.connection.bind('connecting', () => {
            console.log('🔄 Connecting to Reverb...');
        });

        window.Echo.connector.pusher.connection.bind('connected', () => {
            console.log('✅ Connected to Reverb WebSocket server');
        });

        window.Echo.connector.pusher.connection.bind('disconnected', () => {
            console.log('🔌 Disconnected from Reverb');
        });

        window.Echo.connector.pusher.connection.bind('error', (error) => {
            console.error('❌ Reverb connection error:', error);
        });

        window.Echo.connector.pusher.connection.bind('state_change', (states) => {
            console.log('🔀 Connection state changed:', states.previous, '->', states.current);
        });
    } else {
        console.error('❌ Pusher connection not available');
    }

} catch (error) {
    console.error('❌ Failed to create Echo instance:', error);
}