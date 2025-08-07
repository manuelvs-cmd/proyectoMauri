import { defineConfig } from 'vite';
import laravel from 'laravel-vite-plugin';
import tailwindcss from '@tailwindcss/vite';

export default defineConfig({
    plugins: [
        laravel({
            input: [
                'resources/css/app.css',
                'resources/js/app.js'
            ],
            refresh: [
                'resources/views/**',
                'app/Http/Controllers/**',
                'resources/js/**',
                'resources/css/**'
            ],
        }),
        tailwindcss(),
    ],
    build: {
        rollupOptions: {
            output: {
                manualChunks: {
                    vendor: ['axios']
                }
            }
        },
        sourcemap: false,
        minify: 'terser',
    },
    server: {
        host: '0.0.0.0',
        port: 5173,
        strictPort: false,
        hmr: {
            host: 'localhost'
        }
    },
});
