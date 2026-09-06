import { defineConfig } from 'vite';
import laravel from 'laravel-vite-plugin';
import { resolve } from 'path';
import { copyFileSync, mkdirSync } from 'fs';

export default defineConfig({
    // server: {
    //     host: true,
    //     port: 5173,
    //     strictPort: true,
    //     origin: 'http://192.168.0.138:5173',
    //     hmr: {
    //         clientPort: 5173,
    //         host: '192.168.0.138',
    //         protocol: 'ws',
    //     },
    // },
    plugins: [
        {
            name: 'copy-pdf-worker',
            buildStart() {
                mkdirSync('public/build', { recursive: true });
                copyFileSync(
                    resolve('node_modules/pdfjs-dist/build/pdf.worker.mjs'),
                    'public/build/pdf.worker.mjs'
                );
            },
        },
        laravel({
            input: [
                'resources/css/app.css',
                'resources/js/app.js',
                'resources/js/paint.js',
                'resources/js/exam-sync.js',
                'resources/js/exam-timer.js',

            ],
            refresh: true,
        }),
    ],
});
