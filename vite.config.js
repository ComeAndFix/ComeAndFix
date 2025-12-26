import { defineConfig } from 'vite';
import laravel from 'laravel-vite-plugin';

export default defineConfig({
    plugins: [
        laravel({
            input: [
                'resources/css/app.css',
                'resources/js/app.js',
                // Auth CSS
                'resources/css/auth/login.css',
                'resources/css/auth/register.css',
                'resources/css/auth/verify-email.css',
                // Component CSS
                'resources/css/components/buttons.css',
                'resources/css/components/chat.css',
                'resources/css/components/forms.css',
                'resources/css/components/location-modal.css',
                'resources/css/components/navigation.css',
                'resources/css/components/order-details.css',
                'resources/css/components/order-list.css',
                // Customer CSS
                'resources/css/customer/dashboard.css',
                'resources/css/customer/messages.css',
                'resources/css/customer/profile.css',
                'resources/css/customer/tukang-map.css',
                // Tukang CSS
                'resources/css/tukang/complete.css',
                'resources/css/tukang/dashboard.css',
                'resources/css/tukang/finance.css',
                'resources/css/tukang/profile.css',
            ],
            refresh: true,
        }),
    ],
    define: {
        global: 'globalThis',
    },
    optimizeDeps: {
        include: ['laravel-echo']
    },
});
