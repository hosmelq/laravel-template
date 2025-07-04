import './index.css'
import {RouterProvider, toast as heroToast, Toast} from '@heroui/react'
import {createInertiaApp, router, type ResolvedComponent} from '@inertiajs/react'
import {ModalStackProvider} from '@inertiaui/modal-react'
import * as Sentry from '@sentry/react'

import {Modal as ModalLayout} from './layouts/Modal'

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
  layout: () => ModalLayout,
  progress: {
    color: 'var(--accent)',
  },
  title: (title) => (title.length > 0 ? `${title} - Template` : 'Template'),
  resolve: (name) => {
    const pages = import.meta.glob<ResolvedComponent>([
      './pages/**/*.tsx',
      '!./pages/**/_components/**/*.tsx',
    ])
    const page = pages[`./pages/${name}.tsx`]

    if (typeof page === 'undefined') {
      throw new Error(`Page not found: ${name}.`)
    }

    return page()
  },
  strictMode: true,
  withApp: (app) => (
    <RouterProvider navigate={(path, routerOptions) => router.visit(path, routerOptions)}>
      <ModalStackProvider>{app}</ModalStackProvider>
      <Toast.Provider />
    </RouterProvider>
  ),
})
