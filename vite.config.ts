import { defineConfig } from 'vite';
import vue from '@vitejs/plugin-vue';
import { resolve } from 'path';
import symfonyPlugin from "vite-plugin-symfony";

export default defineConfig({
    base: '/build/',
    plugins: [
        vue(),
        symfonyPlugin(),
    ],
    server: {
        host: '0.0.0.0',
        port: 5173,
        hmr: {
            host: 'localhost'
        }
    },
    build: {
        outDir: 'public/build',
        emptyOutDir: true,
        manifest: true,
        rollupOptions: {
            input: {
                crm: resolve(__dirname, 'assets/crm/main.ts')
            }
        }
    }
})
