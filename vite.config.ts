import laravel from 'laravel-vite-plugin'
import {defineConfig} from 'vite'
import checker from 'vite-plugin-checker'
import manifestSRI from 'vite-plugin-manifest-sri'
import tsconfigPaths from 'vite-tsconfig-paths'
import {wayfinder} from '@laravel/vite-plugin-wayfinder'
import {sentryVitePlugin} from '@sentry/vite-plugin'
import tailwindcss from '@tailwindcss/vite'
import react from '@vitejs/plugin-react-oxc'

export default defineConfig(() => {
  return {
    build: {
      cssMinify: 'lightningcss',
      rollupOptions: {
        output: {
          manualChunks(id) {
            if (/node_modules\/.*$/.test(id)) {
              return 'vendor'
            }
          },
        },
      },
      sourcemap: 'hidden',
    },
    plugins: [
      checker({
        typescript: true,
      }),
      laravel({
        input: ['resources/js/app.tsx'],
        refresh: ['resources/lang/**', 'resources/views/**'],
      }),
      manifestSRI(),
      react(),
      tailwindcss(),
      tsconfigPaths(),
      wayfinder({routes: false}),
      sentryVitePlugin({
        authToken: process.env.SENTRY_AUTH_TOKEN,
        org: 'template',
        project: 'template',
      }),
    ],
  }
})
