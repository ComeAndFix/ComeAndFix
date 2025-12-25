import Echo from 'laravel-echo';
import Pusher from 'pusher-js';

window.Pusher = Pusher;

const isReverb = import.meta.env.VITE_REVERB_APP_KEY;

window.Echo = new Echo(
    isReverb
        ? {
            broadcaster: 'reverb',
            key: import.meta.env.VITE_REVERB_APP_KEY,
            wsHost: import.meta.env.VITE_REVERB_HOST,
            wsPort: import.meta.env.VITE_REVERB_PORT,
            wssPort: import.meta.env.VITE_REVERB_PORT,
            forceTLS: (import.meta.env.VITE_REVERB_SCHEME ?? 'https') === 'https',
            enabledTransports: ['ws', 'wss'],
        }
        : {
            broadcaster: 'pusher',
            key: import.meta.env.VITE_PUSHER_APP_KEY,
            cluster: import.meta.env.VITE_PUSHER_APP_CLUSTER,
            forceTLS: true,
        }
);

console.log('Echo initialized with Pusher');

window.Echo.connector.pusher.connection.bind('connecting', () => {
    console.log('WebSocket connecting to Pusher...');
});

window.Echo.connector.pusher.connection.bind('connected', () => {
    console.log('WebSocket connected to Pusher');
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
