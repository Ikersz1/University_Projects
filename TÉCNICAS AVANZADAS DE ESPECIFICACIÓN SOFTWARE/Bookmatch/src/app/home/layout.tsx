'use client'

import Header from '@/components/ui/header'
import NewFooter from '@/components/ui/new-footer'

export default function DefaultLayout({
  children,
}: {
  children: React.ReactNode
}) {
  return (
    <div className="flex flex-col min-h-screen">
      <Header />
      <main className="flex-grow pt-20 md:pt-32">
        {children}
      </main>
      <NewFooter />
    </div>
  )
}
