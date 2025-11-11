import { defineConfig } from 'vite';
import laravel from 'laravel-vite-plugin';

export default defineConfig({
  plugins: [
    laravel({
      input: [
        'resources/scss/progress-bar.scss',
        'resources/scss/rtl-validation.scss',
        'resources/js/app.js',
        'resources/scss/app.scss',

      ],
      refresh: true,
    }),
  ],
  css: {
    preprocessorOptions: {
      scss: {
        api: 'modern-compiler', // Use modern Sass API (Vite 5.4+)
      },
    },
  },
});
