import { defineConfig } from 'vite';
import laravel from 'laravel-vite-plugin';
import react from '@vitejs/plugin-react';
import tailwindcss from '@tailwindcss/vite';
import path from 'node:path';

export default defineConfig({
  plugins: [
    laravel({
      input: {
        website: 'resources/js/website.ts',
        dashboard: 'resources/js/dashboard/main.tsx',
      },
      refresh: true,
    }),
    react(),
    tailwindcss(),
  ],
  resolve: {
    alias: {
      '@': path.resolve(__dirname, 'resources/js'),
      '@dashboard': path.resolve(__dirname, 'resources/js/dashboard'),
      '@website': path.resolve(__dirname, 'resources/js'),
    },
  },
  build: {
    outDir: 'public/build',
    manifest: 'manifest.json',
    chunkSizeWarningLimit: 1200,
    rollupOptions: {
      output: {
        manualChunks(id) {
          if (id.includes('node_modules')) {
            if (id.includes('react')) return 'vendor-react';
            if (id.includes('@tanstack')) return 'vendor-query';
            if (id.includes('inertia')) return 'vendor-inertia';
            if (id.includes('zustand')) return 'vendor-zustand';
            if (id.includes('lucide')) return 'vendor-icons';
            return 'vendor';
          }
        },
      },
    },
  },
  server: {
    watch: { ignored: ['**/storage/framework/views/**'] },
  },
});
