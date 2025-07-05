declare module '@inertiaui/modal-react' {
  import type {PageProps} from '@inertiajs/core'
  import type {InertiaAppProps as ReactInertiaAppProps} from '@inertiajs/react/types/App'
  import type {
    ComponentPropsWithoutRef,
    ComponentType,
    ElementType,
    ForwardRefExoticComponent,
    ReactElement,
    ReactNode,
    RefAttributes,
  } from 'react'

  type ModalPosition = 'center' | 'left' | 'right' | (string & {})

  interface ModalTypeConfiguration {
    closeButton: boolean
    closeExplicitly: boolean
    maxWidth: string | null
    paddingClasses: string
    panelClasses: string
    position: ModalPosition
  }

  interface ModalGlobalConfiguration {
    type: 'modal' | 'slideover'
    navigate: boolean
    modal: ModalTypeConfiguration
    slideover: ModalTypeConfiguration
  }

  interface ModalBaseConfigOverrides {
    slideover?: boolean
    closeButton?: boolean
    closeExplicitly?: boolean
    maxWidth?: string | null
    paddingClasses?: string
    panelClasses?: string
    position?: ModalPosition
  }

  type ModalConfiguration = ModalBaseConfigOverrides & Record<string, unknown>

  interface ModalReloadOptions {
    only?: string[] | string
    except?: string[] | string
    method?: string
    data?: Record<string, unknown>
    headers?: Record<string, unknown>
    onStart?: (...args: unknown[]) => void
    onSuccess?: (...args: unknown[]) => void
    onError?: (...args: unknown[]) => void
    onFinish?: (...args: unknown[]) => void
  }

  interface ModalResponseMeta {
    deferredProps?: Record<string, string[]>
    [key: string]: unknown
  }

  interface ModalResponseData {
    id?: string
    component: string
    props: Record<string, unknown>
    url?: string
    version?: string
    meta?: ModalResponseMeta
  }

  type ModalEventHandler = (...args: unknown[]) => void

  interface ModalInstance {
    id: string
    index: number
    isOpen: boolean
    shouldRender: boolean
    onTopOfStack: boolean
    component: ComponentType<any> | null
    config: ModalConfiguration
    props: Record<string, unknown>
    close(): void
    afterLeave(): void
    reload(options?: ModalReloadOptions): void
    setOpen(open: boolean): void
    emit(event: string, ...args: unknown[]): void
    getParentModal(): ModalInstance | null
    getChildModal(): ModalInstance | null
  }

  interface ModalChildrenRenderProps {
    afterLeave: () => void
    close: () => void
    config: ModalConfiguration
    emit: (event: string, ...args: unknown[]) => void
    getChildModal: () => ModalInstance | null
    getParentModal: () => ModalInstance | null
    id: string
    index: number
    isOpen: boolean
    modalContext: ModalInstance
    onTopOfStack: boolean
    reload: (options?: ModalReloadOptions) => void
    setOpen: (open: boolean) => void
    shouldRender: boolean
  }

  interface LocalModalRegistration {
    name: string
    callback: (modal: ModalInstance) => void
  }

  interface ModalVisitOptions {
    method?: string
    data?: Record<string, unknown>
    headers?: Record<string, unknown>
    config?: ModalConfiguration
    onClose?: () => void
    onAfterLeave?: () => void
    queryStringArrayFormat?: 'indices' | 'brackets' | 'repeat' | 'comma' | (string & {})
    navigate?: boolean
    onStart?: (...args: unknown[]) => void
    onSuccess?: (...args: unknown[]) => void
    onError?: (...args: unknown[]) => void
    listeners?: Record<string, ModalEventHandler>
  }

  interface ModalStackContextValue {
    stack: ModalInstance[]
    localModals: Record<string, LocalModalRegistration>
    push(
      component: ComponentType<any> | null,
      response: ModalResponseData,
      config?: ModalConfiguration,
      onClose?: (() => void) | null,
      onAfterLeave?: (() => void) | null,
    ): ModalInstance
    pushFromResponseData(
      responseData: ModalResponseData,
      config?: ModalConfiguration,
      onClose?: (() => void) | null,
      onAfterLeave?: (() => void) | null,
    ): Promise<ModalInstance>
    closeAll(): void
    reset(): void
    length(): number
    visit(
      href: string,
      method?: string,
      data?: Record<string, unknown>,
      headers?: Record<string, unknown>,
      config?: ModalConfiguration,
      onClose?: (() => void) | null,
      onAfterLeave?: (() => void) | null,
      queryStringArrayFormat?: 'indices' | 'brackets' | 'repeat' | 'comma' | (string & {}),
      navigate?: boolean,
      onStart?: (...args: unknown[]) => void,
      onSuccess?: (...args: unknown[]) => void,
      onError?: (...args: unknown[]) => void,
    ): Promise<ModalInstance>
    visitModal(url: string, options?: ModalVisitOptions): Promise<ModalInstance>
    registerLocalModal(name: string, callback: (modal: ModalInstance) => void): void
    removeLocalModal(name: string): void
  }

  type ModalChildren = ReactNode | ((props: ModalChildrenRenderProps) => ReactNode)

  interface HeadlessModalProps extends ModalConfiguration {
    name?: string
    children?: ModalChildren
    onFocus?: () => void
    onBlur?: () => void
    onClose?: () => void
    onSuccess?: () => void
  }

  interface ModalProps extends HeadlessModalProps {
    onAfterLeave?: () => void
  }

  interface DeferredProps {
    data: string | string[]
    fallback?: ReactNode
    children: ReactNode
  }

  interface WhenVisibleProps {
    children: ReactNode
    data?: string | string[]
    params?: ModalReloadOptions
    buffer?: number
    as?: ElementType
    always?: boolean
    fallback?: ReactNode
  }

  type ModalLinkChildren = ReactNode | ((state: {loading: boolean}) => ReactNode)

  type ModalLinkProps<T extends ElementType = 'a'> = ModalConfiguration &
    Omit<ComponentPropsWithoutRef<T>, 'href' | 'children'> &
    Record<string, unknown> & {
      as?: T
      href: string
      method?: string
      data?: Record<string, unknown>
      headers?: Record<string, unknown>
      queryStringArrayFormat?: 'indices' | 'brackets' | 'repeat' | 'comma' | (string & {})
      onAfterLeave?: () => void
      onBlur?: () => void
      onClose?: () => void
      onError?: (error: unknown) => void
      onFocus?: () => void
      onStart?: () => void
      onSuccess?: () => void
      navigate?: boolean
      children?: ModalLinkChildren
    }

  type InertiaAppProps<SharedProps extends PageProps = PageProps> =
    ReactInertiaAppProps<SharedProps>

  type InitPageProps<SharedProps extends PageProps = PageProps> = Partial<
    InertiaAppProps<SharedProps>
  >

  const modalPropNames: readonly [
    'closeButton',
    'closeExplicitly',
    'maxWidth',
    'paddingClasses',
    'panelClasses',
    'position',
    'slideover',
  ]

  function initFromPageProps<SharedProps extends PageProps = PageProps>(
    props: InitPageProps<SharedProps>,
  ): void
  function putConfig(config: Partial<ModalGlobalConfiguration>): void
  function putConfig<TKey extends string>(key: TKey, value: unknown): void
  function renderApp<SharedProps extends PageProps = PageProps>(
    App: ComponentType<InertiaAppProps<SharedProps>>,
    props: InertiaAppProps<SharedProps>,
  ): ReactElement
  function getConfig(): ModalGlobalConfiguration
  function getConfig<TKey extends string>(key: TKey): unknown
  function getConfigByType(isSlideover: boolean, key: keyof ModalTypeConfiguration): unknown
  function resetConfig(): void
  function setPageLayout<TModule extends {default: ComponentType<any>}>(
    layout: ComponentType<any>,
  ): (module: TModule) => TModule

  const ModalStackProvider: ComponentType<{children?: ReactNode}>
  const ModalRoot: ComponentType<{children?: ReactNode}>
  const HeadlessModal: ForwardRefExoticComponent<
    HeadlessModalProps & RefAttributes<ModalInstance | null>
  >
  const Modal: ForwardRefExoticComponent<ModalProps & RefAttributes<ModalInstance | null>>
  function ModalLink<T extends ElementType = 'a'>(props: ModalLinkProps<T>): ReactElement | null
  const Deferred: ComponentType<DeferredProps>
  const WhenVisible: ComponentType<WhenVisibleProps>

  function useModalStack(): ModalStackContextValue
  function useModal(): ModalInstance | null
  function useModalIndex(): number
}
