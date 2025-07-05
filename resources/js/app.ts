import './index.css'
import {createApp, h} from 'vue'
import {createInertiaApp} from '@inertiajs/vue3'
import * as Sentry from '@sentry/vue'

void createInertiaApp({
  title: (title) => `${title} - Template`,
  resolve: (name) => {
    const pages = import.meta.glob('./pages/**/*.vue', {eager: true})

    return pages[`./pages/${name}.vue`] as any
  },
  setup({App, el, plugin, props}) {
    const app = createApp({
      render: () => h(App, props),
    }).use(plugin)

    Sentry.init({
      app,
      dsn: import.meta.env.VITE_SENTRY_DSN,
      integrations: [
        Sentry.browserProfilingIntegration(),
        Sentry.browserTracingIntegration(),
        Sentry.captureConsoleIntegration(),
        Sentry.replayIntegration(),
      ],
      replaysOnErrorSampleRate: 1.0,
      replaysSessionSampleRate: 0.25,
      tracesSampleRate: 0.25,
    })

    app.mount(el)
  },
})
