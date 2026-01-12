import { defineConfig } from 'vite';
import laravel from 'laravel-vite-plugin';
import tailwindcss from '@tailwindcss/vite';

export default defineConfig({
    plugins: [
        laravel({
            input: ['resources/css/app.css', 'resources/js/app.js'],
            refresh: true,
        }),
        tailwindcss(),
    ],
    server: {
        host: '0.0.0.0', // PENTING: Biar bisa diakses dari luar container
        port: 5173,      // Port standar Vite
        strictPort: true,
        hmr: {
            host: 'localhost', // Browser di Windows tahunya server ini ada di 'localhost'
        },
        watch: {
            usePolling: true, // WAJIB DI WINDOWS: Karena notifikasi perubahan file dari Windows ke Linux sering macet
        },
    },
});
