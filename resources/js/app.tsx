import './index.css'
import {toast as heroToast, Toast} from '@heroui/react'
import {createInertiaApp, router} from '@inertiajs/react'
import {renderApp} from '@inertiaui/modal-react'
import * as Sentry from '@sentry/react'
import {StrictMode, type ComponentType, type ReactElement, type ReactNode} from 'react'
import {RouterProvider} from 'react-aria-components'
import {createRoot} from 'react-dom/client'

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

router.on('flash', (event) => {
  const {toast} = event.detail.flash

  if (!toast) {
    return
  }

  heroToast(toast.title, {
    description: toast.description,
    timeout: toast.timeout,
    variant: toast.variant,
  })
})

void createInertiaApp({
  title: (title) => `${title} - Template`,
  resolve: (name) => {
    const pages = import.meta.glob<{
      default: ComponentType & {layout?: (page: ReactElement) => ReactNode}
    }>('./pages/**/*.tsx')
    const page = pages[`./pages/${name}.tsx`]

    if (typeof page === 'undefined') {
      throw new Error(`Page not found: ${name}.`)
    }

    return page()
  },
  setup({App, el, props}) {
    const root = createRoot(el)

    root.render(
      <StrictMode>
        <RouterProvider navigate={(path, routerOptions) => router.visit(path, routerOptions)}>
          {renderApp(App, props)}
          <Toast.Provider />
        </RouterProvider>
      </StrictMode>,
    )
  },
})
