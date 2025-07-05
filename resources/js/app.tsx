import './index.css'
import {StrictMode} from 'react'
import {createRoot} from 'react-dom/client'
import {HeroUIProvider, ToastProvider} from '@heroui/react'
import {createInertiaApp} from '@inertiajs/react'
import * as Sentry from '@sentry/react'

Sentry.init({
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

void createInertiaApp({
  title: (title) => `${title} - Template`,
  resolve: (name) => {
    const pages = import.meta.glob('./pages/**/*.tsx', {eager: true})

    return pages[`./pages/${name}.tsx`] as any
  },
  setup({App, el, props}) {
    const root = createRoot(el)

    root.render(
      <StrictMode>
        <HeroUIProvider labelPlacement="outside" validationBehavior="aria">
          <App {...props} />
          <ToastProvider placement="bottom-right" />
        </HeroUIProvider>
      </StrictMode>,
    )
  },
})
