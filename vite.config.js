import { defineConfig } from 'vite';
import laravel from 'laravel-vite-plugin';
import tailwindcss from '@tailwindcss/vite';
import inject from '@rollup/plugin-inject';

export default defineConfig({
    plugins: [
        laravel({
            input: [
                'resources/css/app.css',
                'resources/css/pages/login.css',
                'resources/js/app.js',
                'resources/js/modules/users/index.js',
                'resources/js/modules/roles/index.js',
            ],
            refresh: true,
        }),
        tailwindcss(),
        inject({
            $: 'jquery',
            jQuery: 'jquery',
            include: ['**/*.js'],
        }),
    ],
    optimizeDeps: {
        include: ['jquery'],
    },
    server: {
        watch: {
            ignored: ['**/storage/framework/views/**'],
        },
    },
});
