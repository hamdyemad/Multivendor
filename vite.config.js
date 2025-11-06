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
        // Vendor Module Assets
        'Modules/Vendor/resources/assets/css/vendor-form.css',
        'Modules/Vendor/resources/assets/js/vendor-form.js',

        // CatalogManagement Module Assets
        'Modules/CatalogManagement/resources/css/tree-view.css',
        'Modules/CatalogManagement/resources/assets/sass/product-form.scss',
        'Modules/CatalogManagement/resources/assets/js/product-form.js',
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
