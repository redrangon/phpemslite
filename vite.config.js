// vite.config.js
import { defineConfig } from 'vite';
import vue from '@vitejs/plugin-vue';
import path from 'path';
import wasm from 'vite-plugin-wasm';

export default defineConfig({
    plugins: [
        vue(),
        wasm()
    ],
    base: './',
    publicDir:false,
    build: {
        outDir: 'public/assets',
        emptyOutDir: true,
        target: 'esnext',
        rollupOptions: {
            input: {
                main: path.resolve(__dirname, 'frontend/main.js')
            },
            output: {
                entryFileNames: '[name]-[hash].js',
                chunkFileNames: '[name]-[hash].js',
                assetFileNames: '[name]-[hash].[ext]'
            }
        }
    },
    server: {
        host: '0.0.0.0',
        port: 5173,
        proxy: {
            '/api': {
                target: 'http://127.0.0.1',
                changeOrigin: true,
                secure: false,
                rewrite: (path) => path.replace(/^\/api/, '')
            }
        }
    },
    resolve: {
        alias: {
            '@': path.resolve(__dirname, 'frontend')
        }
    },
    optimizeDeps: {
        include: ['vue', 'vue-router', 'pinia', 'axios']
    }
});