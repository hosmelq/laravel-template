import type {VisitOptions} from '@inertiajs/core'

import type {ToastVariant} from '#/types/generated/enums'
import type {PageProps as LocalPageProps} from '#/types/laravel'

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
