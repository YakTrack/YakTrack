import { defineConfig } from 'vite';
import laravel from 'laravel-vite-plugin';
import vue from '@vitejs/plugin-vue';
import path from 'path';

export default defineConfig({
    plugins: [
        laravel({
            input: [
                'resources/css/app.css',
                'resources/js/app.js',
            ],
            refresh: true,
            publicDirectory: 'public',
        }),
        vue({
            template: {
                transformAssetUrls: {
                    base: null,
                    includeAbsolute: false,
                },
            },
        }),
    ],
    resolve: {
        alias: {
            '@': path.resolve('resources/js'),
        },
    },
    build: {
        chunkSizeWarningLimit: 1600,
        rollupOptions: {
            output: {
                manualChunks: undefined,
                assetFileNames: (assetInfo) => {
                    if (assetInfo.name && assetInfo.name.match(/\.(woff|woff2|eot|ttf|otf)$/)) {
                        return 'fonts/[name].[ext]';
                    }
                    return 'assets/[name]-[hash].[ext]';
                },
            },
        },
    },
    server: {
        host: '0.0.0.0',
        port: 5173,
        cors: {
            origin: ['http://localhost:5173', 'http://yak-track.test', 'https://yak-track.test'],
            credentials: true,
        },
        hmr: {
            host: 'localhost',
        },
        origin: 'http://localhost:5173',
    },
});