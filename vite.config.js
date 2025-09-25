import {defineConfig} from 'vite';
import laravel from 'laravel-vite-plugin';
import vue from '@vitejs/plugin-vue2';
import tailwindcss from '@tailwindcss/vite'

export default defineConfig({
    publicDir: 'public',
    // css: {
    //     transformer: 'lightningcss',
    // },
    build: {
        // rollupOptions: {
        //     treeshake: 'smallest'
        // },
        // cssMinify: 'esbuild',
        // minify: 'esbuild',
    },
    plugins: [
        laravel([
            'resources/css/app.css',
            'resources/js/app.js',
        ]),
        tailwindcss(),
        vue({
            template: {
                transformAssetUrls: {
                    base: null,
                    includeAbsolute: false,
                },
            },
        }),
    ],
});
