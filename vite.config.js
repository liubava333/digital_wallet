import { defineConfig } from 'vite';
import laravel from 'laravel-vite-plugin';
import vue from '@vitejs/plugin-vue';
import path from 'path'; // 1. Импортируем модуль path

export default defineConfig({
    plugins: [
        laravel({
            input: 'resources/js/app.js',
            refresh: true,
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
            // 2. Настраиваем алиас @ для папки src
            '@': path.resolve(__dirname, './resources/js'),
        },
    },
    server: {
        cors: true, // Включает CORS-заголовки
        host: '0.0.0.0', // Позволяет принимать соединения внутри Docker
        hmr: {
            host: 'localhost',
        },
    },
});
