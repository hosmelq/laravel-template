import laravel from 'laravel-vite-plugin'
import {defineConfig} from 'vite'
import checker from 'vite-plugin-checker'
import manifestSRI from 'vite-plugin-manifest-sri'
import {run} from 'vite-plugin-run'
import tsconfigPaths from 'vite-tsconfig-paths'
import {sentryVitePlugin} from '@sentry/vite-plugin'
import tailwindcss from '@tailwindcss/vite'
import react from '@vitejs/plugin-react-swc'

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
      run({
        name: 'ziggy',
        pattern: ['routes/**/*.php'],
        run: ['composer', 'ziggy'],
      }),
      react(),
      tailwindcss(),
      tsconfigPaths(),
      sentryVitePlugin({
        authToken: process.env.SENTRY_AUTH_TOKEN,
        org: 'template',
        project: 'template',
      }),
    ],
  }
})
