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
        port: 5173,      
        hmr: {
            host: 'localhost', 
        },
    },
});

// import { defineConfig } from 'vite';
// import laravel from 'laravel-vite-plugin';

// export default defineConfig({
//     server: {
//         host: '0.0.0.0', // Izinkan akses dari luar container (laptop Anda)
//         port: 5173,      // Port standar Vite
//         hmr: {
//             host: 'localhost', // Browser di laptop akan connect ke sini
//         },
//         watch: {
//             usePolling: true, // Wajib untuk Docker di Windows/WSL agar perubahan file terdeteksi
//         },
//     },
//     plugins: [
//         laravel({
//             input: ['resources/css/app.css', 'resources/js/app.js'],
//             refresh: true,
//         }),
//     ],
// });