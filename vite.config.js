import vue from '@vitejs/plugin-vue'
import vueJsx from '@vitejs/plugin-vue-jsx'
import laravel from 'laravel-vite-plugin'
import { fileURLToPath } from 'node:url'
import AutoImport from 'unplugin-auto-import/vite'
import Components from 'unplugin-vue-components/vite'
import { defineConfig } from 'vite'
import vuetify from 'vite-plugin-vuetify'
import svgLoader from 'vite-svg-loader'

export default defineConfig({
  // ❌ Remove '/build/' to prevent wrong base path in production
  base: '/',

  plugins: [
    vue({
      template: {
        transformAssetUrls: {
          base: '/',
          includeAbsolute: false,
        },
      },
    }),
    vueJsx(),
    laravel({
      input: ['resources/js/main.js'],
      refresh: true,
      buildDirectory: 'assets', // ✅ tells Laravel to look for /assets, not /build
    }),
    vuetify({
      styles: {
        configFile: 'resources/styles/variables/_vuetify.scss',
      },
    }),
    Components({
      dirs: ['resources/js/@core/components', 'resources/js/components'],
      dts: true,
      resolvers: [
        componentName => {
          if (componentName === 'VueApexCharts') {
            return { name: 'default', from: 'vue3-apexcharts', as: 'VueApexCharts' }
          }
        },
      ],
    }),
    AutoImport({
      imports: ['vue', 'vue-router', '@vueuse/core', '@vueuse/math', 'pinia'],
      vueTemplate: true,
      ignore: ['useCookies', 'useStorage'],
      eslintrc: {
        enabled: true,
        filepath: './.eslintrc-auto-import.json',
      },
    }),
    svgLoader()
  ],

  define: {
    'process.env': {},
  },

  resolve: {
    alias: {
      '@core-scss': fileURLToPath(new URL('./resources/styles/@core', import.meta.url)),
      '@': fileURLToPath(new URL('./resources/js', import.meta.url)),
      '@core': fileURLToPath(new URL('./resources/js/@core', import.meta.url)),
      '@layouts': fileURLToPath(new URL('./resources/js/@layouts', import.meta.url)),
      '@images': fileURLToPath(new URL('./resources/images/', import.meta.url)),
      '@styles': fileURLToPath(new URL('./resources/styles/', import.meta.url)),
      '@configured-variables': fileURLToPath(new URL('./resources/styles/variables/_template.scss', import.meta.url)),
    },
  },

  build: {
    chunkSizeWarningLimit: 5000,
    outDir: 'public/assets',  // ✅ build output folder
    assetsDir: '',            // ✅ avoids nested dirs like /assets/assets
    emptyOutDir: false,
  },

  optimizeDeps: {
    exclude: ['vuetify'],
    entries: ['./resources/js/**/*.vue'],
  },
})
