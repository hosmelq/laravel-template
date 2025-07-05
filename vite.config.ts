import {wayfinder} from '@laravel/vite-plugin-wayfinder'
import {sentryVitePlugin} from '@sentry/vite-plugin'
import tailwindcss from '@tailwindcss/vite'
import react from '@vitejs/plugin-react'
import laravel from 'laravel-vite-plugin'
import {defineConfig, loadEnv} from 'vite'
import manifestSRI from 'vite-plugin-manifest-sri'
import tsconfigPaths from 'vite-tsconfig-paths'

export default defineConfig(({mode}) => {
  const env = loadEnv(mode, process.cwd(), '')

  return {
    build: {
      cssMinify: 'lightningcss',
      sourcemap: 'hidden',
    },
    plugins: [
      laravel({
        input: ['resources/js/app.tsx'],
        refresh: ['resources/lang/**', 'resources/views/**'],
      }),
      manifestSRI(),
      react({
        babel: {
          plugins: [['babel-plugin-react-compiler']],
        },
      }),
      tailwindcss(),
      tsconfigPaths(),
      wayfinder({routes: false}),
      sentryVitePlugin({
        authToken: env.SENTRY_AUTH_TOKEN,
        org: 'template',
        project: 'template',
        release: {
          name: env.RAILWAY_GIT_COMMIT_SHA,
        },
      }),
    ],
  }
})
