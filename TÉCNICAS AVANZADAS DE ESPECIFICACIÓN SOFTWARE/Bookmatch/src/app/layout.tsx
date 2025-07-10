import './globals.css'
import { Inter, Inter_Tight } from 'next/font/google'
// Remove the AuthProvider import since we're not using it
// import { AuthProvider } from '@/context/AuthContext'

const inter = Inter({
  subsets: ['latin'],
  variable: '--font-inter',
  display: 'swap'
})

const inter_tight = Inter_Tight({
  weight: ['500', '600', '700'],
  style: ['normal', 'italic'],
  subsets: ['latin'],
  variable: '--font-inter-tight',
  display: 'swap'
})

export const metadata = {
  title: 'BookMatch',
  description: 'Encuentra tus libros favoritos',
}

export default function RootLayout({
  children,
}: {
  children: React.ReactNode
}) {
  return (
    <html lang="en">
      <body className={`${inter.variable} ${inter_tight.variable} font-inter antialiased bg-white text-zinc-900 tracking-tight`}>
        <div className="flex flex-col min-h-screen overflow-hidden supports-[overflow:clip]:overflow-clip">
          {/* Remove the AuthProvider wrapper */}
          {children}
        </div>
      </body>
    </html>
  )
}
