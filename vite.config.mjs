import { defineConfig } from 'vite';
import vue from '@vitejs/plugin-vue2';
import path from 'path';

export default defineConfig({
  plugins: [vue()],
  root: 'resources',
  base: '/dist/',
  build: {
    outDir: path.resolve('dist'),
    emptyOutDir: true,
    rollupOptions: {
      input: {
        app: path.resolve('resources/js/index.js'),
        style: path.resolve('resources/css/main.css'),
      },
      output: {
        entryFileNames: 'js/statamic-one-click-content-translation.js',
        assetFileNames: ({ name }) => {
          if (name && name.endsWith('.css')) {
            return 'css/statamic-one-click-content-translation.css';
          }
          return 'assets/[name].[hash][extname]';
        },
      },
    },
  },
  resolve: {
    alias: {
      '@': path.resolve('resources/js'),
    },
  },
  css: {
    preprocessorOptions: {
      scss: {},
    },
  },
});
