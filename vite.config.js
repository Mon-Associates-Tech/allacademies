import { defineConfig } from 'vite';
import laravel from 'laravel-vite-plugin';

export default defineConfig({
    server: {
        host: true,
        port: 5173,
        strictPort: true,
        origin: 'http://192.168.0.169:5173',
        hmr: {
            clientPort: 5173,
            host: '192.168.0.169',
            protocol: 'ws',
        },
    },
    plugins: [
        laravel({
            input: ['resources/css/app.css', 'resources/js/app.js', 'resources/js/paint.js', 'resources/js/exam-heartbeat.js', 'resources/js/exam-timer.js', 'resources/js/exam-sync.js'],
            refresh: true,
        }),
    ],
});
