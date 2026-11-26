import { defineConfig, loadEnv } from 'vite';
import laravel from 'laravel-vite-plugin';
import { resolve } from 'path';
import { copyFileSync, existsSync, mkdirSync, readFileSync } from 'fs';

export default defineConfig(({ mode }) => {
    const env = loadEnv(mode, process.cwd(), '');
    const laragonSslDirectory = 'C:\\laragon\\etc\\ssl';
    const certificatePath = env.VITE_DEV_SERVER_CERT || resolve(laragonSslDirectory, 'laragon.crt');
    const privateKeyPath = env.VITE_DEV_SERVER_KEY || resolve(laragonSslDirectory, 'laragon.key');
    const https = existsSync(certificatePath) && existsSync(privateKeyPath)
        ? {
            cert: readFileSync(certificatePath),
            key: readFileSync(privateKeyPath),
        }
        : undefined;

    return {
        build: {
            target: 'es2020',
        },
        server: {
            host: 'localhost',
            ...(https ? { https } : {}),
        },
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
                    'resources/js/ebook_reader.js',
                    'resources/js/paint.js',
                    'resources/js/exam-sync.js',
                    'resources/js/exam-timer.js',
                    // 'resources/js/tiptap-editor.js',
                    // 'resources/js/layout-editor.js',
                ],
                refresh: true,
            }),
        ],
    };
});
