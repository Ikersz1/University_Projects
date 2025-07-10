'use client'

import { useRef, useState, useEffect, useCallback } from 'react'

interface CounterProps {
  number: number
  duration?: number
}

export default function Counter({ number = 0, duration = 3000 }: CounterProps) {
  const counterElement = useRef<HTMLSpanElement | null>(null)
  const startTimestamp = useRef<number | null>(null)
  const animationRequestId = useRef<number | null>(null)
  const observerRef = useRef<IntersectionObserver | null>(null)
  const [counterValue, setCounterValue] = useState<string>('0')
  const [animationCompleted, setAnimationCompleted] = useState<boolean>(false)

  const precision: number = (number % 1 === 0)
    ? 0
    : (number.toString().split('.')[1] || []).length

  const easeOut = (t: number): number => {
    return 1 - Math.pow(1 - t, 5)
  }

  const startAnimation = useCallback(() => {
    const step = (timestamp: number) => {
      if (!startTimestamp.current) startTimestamp.current = timestamp
      const progress: number = Math.min((timestamp - startTimestamp.current) / duration, 1)
      const easedProgress: number = easeOut(progress)
      const newRawValue: number = parseFloat((easedProgress * number).toFixed(precision))
      setCounterValue(newRawValue.toFixed(precision))

      if (progress < 1) {
        animationRequestId.current = window.requestAnimationFrame(step)
      } else {
        setAnimationCompleted(true)
      }
    }

    animationRequestId.current = window.requestAnimationFrame(step)
  }, [number, duration, precision])

  useEffect(() => {
    if (!counterElement.current || animationCompleted) return

    observerRef.current = new IntersectionObserver(entries => {
      entries.forEach(entry => {
        if (entry.isIntersecting && !animationCompleted) {
          startAnimation()
        }
      })
    })

    observerRef.current.observe(counterElement.current)

    return () => {
      if (animationRequestId.current) {
        window.cancelAnimationFrame(animationRequestId.current)
      }
      if (observerRef.current) {
        observerRef.current.disconnect()
      }
    }
  }, [animationCompleted, startAnimation])

  return (
    <span ref={counterElement}>{counterValue}</span>
  )
}
