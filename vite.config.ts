import { wayfinder } from '@laravel/vite-plugin-wayfinder';
import tailwindcss from '@tailwindcss/vite';
import vue from '@vitejs/plugin-vue';
import laravel from 'laravel-vite-plugin';
import { defineConfig } from 'vite';

export default defineConfig({
    // server: {
    //     host: '0.0.0.0',
    //     strictPort: true,
    //     port: 5173,
    //     hmr: {
    //         host: '192.168.100.185',
    //     },
    // },

    plugins: [
        laravel({
            input: ['resources/css/app.css', 'resources/js/app.ts'],
            ssr: 'resources/js/ssr.ts',
            refresh: true,
        }),

        tailwindcss('./tailwind.config.ts'),

        wayfinder({
            formVariants: true,
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

    // ✅ Correct consolidated optimizeDeps block
    optimizeDeps: {
        include: [
            'ably',
            '@vuepic/vue-datepicker'
        ],
    },
});
