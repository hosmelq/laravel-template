import type {VisitOptions} from '@inertiajs/core'

import type {PageProps as LocalPageProps, ToastVariant} from '#/types/laravel'

declare module '@inertiajs/core' {
  export interface InertiaConfig {
    flashDataType: {
      toast?: {
        variant: ToastVariant
        description?: string
        timeout: number
        title: string
      }
    }
  }

  interface PageProps extends LocalPageProps {}
}

declare module '@react-types/shared' {
  interface RouterConfig {
    routerOptions: VisitOptions
  }
}
