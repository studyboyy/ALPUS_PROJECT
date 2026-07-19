import {
    defineConfig
} from 'vite';
import laravel from 'laravel-vite-plugin';
import tailwindcss from "@tailwindcss/vite";

export default defineConfig({
    plugins: [
        laravel({
            input: ['resources/css/app.css', 'resources/js/app.js'],
            refresh: false,
        }),
        tailwindcss(),
    ],
    server: {
        // Keep dev assets on the same IPv4 interface as `php artisan serve`.
        host: '127.0.0.1',
        hmr: {
            host: '127.0.0.1',
        },
        cors: true,
        watch: {
            ignored: ['**/storage/framework/views/**'],
        },
    },
});
