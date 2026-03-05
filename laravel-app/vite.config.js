import { defineConfig } from 'vite';
import laravel from 'laravel-vite-plugin';
import react from '@vitejs/plugin-react';

export default defineConfig({
    plugins: [
        laravel({
            input: ['resources/js/app.jsx', 'resources/js/alert.js'],
            refresh: true,
        }),
        react(),
    ],
    server: {
        host: '0.0.0.0', // Важно для Docker!
        port: 5173,
        strictPort: true, // Запрещаем прыгать на 5174
        hmr: {
            host: 'localhost', // Как браузер будет обращаться к Vite снаружи
        },
    },
});
