import { defineConfig } from 'vite';
import laravel from 'laravel-vite-plugin';
import tailwindcss from '@tailwindcss/vite';

export default defineConfig({
    server: {
        host: true,
        hmr: 'https://xn40l4b6jz.sharedwithexpose.com',
    },
    plugins: [
        laravel({
            input: ['resources/css/app.css', 'resources/css/filament/app/theme.css', 'resources/js/app.js'],
            refresh: true,
        }),
        tailwindcss(),
    ],
});
