import { defineConfig } from 'vite';
import vue from '@vitejs/plugin-vue2';

export default defineConfig({
    plugins: [
        vue(),
    ],
    resolve: {
        alias: {
            vue: 'vue/dist/vue.esm.js',
            '@': '/resources/js',
        },
    },
    server: {
        port: 5173,
        host: '0.0.0.0',
        hmr: {
            host: 'localhost',
        },
    },
    build: {
        outDir: 'public/build',
        assetsDir: 'assets',
        manifest: true,
        rollupOptions: {
            input: 'resources/js/app.js',
        },
    },
});
