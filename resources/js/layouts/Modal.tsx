import {ModalRoot} from '@inertiaui/modal-react'
import type {ReactNode} from 'react'

interface ModalProps {
  children: ReactNode
}

export function Modal(props: ModalProps) {
  return (
    <>
      {props.children}
      <ModalRoot />
    </>
  )
}
