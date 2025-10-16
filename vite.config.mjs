import { defineConfig } from 'vite';
import laravel from 'laravel-vite-plugin';
import vue from '@vitejs/plugin-vue';
import path from 'path';

export default defineConfig({
    plugins: [
        laravel({
            input: ['resources/js/app.js'],
            refresh: true,
        }),
        vue({
            template: {
                transformAssetUrls: {
                    base: null,
                    includeAbsolute: false,
                },
            },
            compilerOptions: {
                isCustomElement: (tag) => {
                    return tag.includes(':') // Treat any tag with colons as custom elements
                }
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
        hmr: {
            host: 'yaktrack.test',
        },
    },
    test: {
        environment: 'node',
        include: [
            'resources/js/tests/**/*.{test,spec}.js'
        ],
        exclude: [
            'node_modules/**',
            'e2e/**',
            'test-results/**',
            'playwright-report/**'
        ],
        globals: true,
        alias: {
            '@': path.resolve('resources/js'),
        },
    },
});