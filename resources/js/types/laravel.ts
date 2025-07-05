import type {ErrorBag, Errors, VisitOptions} from '@inertiajs/core'

/* Types */

export interface Pagination<T> {
  data: T[]
  links: {
    first: string
    last: string
    next: null | string
    prev: null | string
  }
  meta: {
    current_page: number
    from: null
    last_page: number
    links: {
      active: boolean
      label: string
      url: null | string
    }[]
    path: string
    per_page: number
    to: null
    total: number
  }
}

/* Enums */

export type ToastVariant = 'accent' | 'danger' | 'default' | 'success' | 'warning'

/* Inertia */

export interface PageProps {
  [key: string]: unknown
  auth: {
    user: null | UserResource
  }
  context: {
    adult_hub_url: string
    base_domain: string
    environment: string
    is_youth_domain: boolean
    youth_hub_url: string
  }
  deferred?: Record<string, VisitOptions['only']>
  errors: Errors & ErrorBag
}

export interface AuthenticatedPageProps extends PageProps {
  auth: {
    user: UserResource
  }
}

/* Resources */

interface Resource {
  created_at: string
  id: number
  updated_at: string
}

export interface UserResource extends Resource {
  avatar_url: string
  email: string
  first_name: string
  is_email_verified: boolean
  last_name: string
  name: string
}
