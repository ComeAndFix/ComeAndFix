import Echo from 'laravel-echo';
import Pusher from 'pusher-js';

window.Pusher = Pusher;

window.Echo = new Echo({
    broadcaster: 'reverb',
    key: import.meta.env.VITE_REVERB_APP_KEY || 'mfrc5wameuf3rnhsmfra',
    wsHost: import.meta.env.VITE_REVERB_HOST || '127.0.0.1',
    wsPort: import.meta.env.VITE_REVERB_PORT || 8080,
    wssPort: import.meta.env.VITE_REVERB_PORT || 8080,
    forceTLS: false,
    enabledTransports: ['ws', 'wss'],
});

console.log('Echo initialized with Reverb using Pusher client');

window.Echo.connector.pusher.connection.bind('connecting', () => {
    console.log('WebSocket connecting to Reverb...');
});

window.Echo.connector.pusher.connection.bind('connected', () => {
    console.log('WebSocket connected to Reverb');
});

window.Echo.connector.pusher.connection.bind('disconnected', () => {
    console.log('WebSocket disconnected');
});

window.Echo.connector.pusher.connection.bind('error', (error) => {
    console.error('WebSocket error:', error);
});

window.Echo.connector.pusher.connection.bind('unavailable', () => {
    console.error('WebSocket unavailable');
});

window.Echo.connector.pusher.connection.bind('failed', () => {
    console.error('WebSocket connection failed');
});
