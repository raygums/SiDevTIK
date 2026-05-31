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
        host: '0.0.0.0', 
        port: process.env.VITE_PORT ? parseInt(process.env.VITE_PORT) : 5173,      
        strictPort: true,
        hmr: {
            host: 'localhost',
            clientPort: process.env.VITE_PORT ? parseInt(process.env.VITE_PORT) : 5173,
        },
        watch: {
            usePolling: true, 
        },
    },
});
