"use client"

import Link from "next/link"
import Image from "next/image"
import Logo from "@/public/images/logo.png"
import { useEffect, useState } from "react"
import { onAuthStateChanged, signOut, type User } from "firebase/auth"
import { auth } from "../../firebaseConfig"
import { UserCircle, BookOpen, Menu, X, ChevronDown } from "lucide-react"
import SearchBox from '@/components/search/SearchBox'

export default function Header() {
  const [user, setUser] = useState<User | null>(null)
  const [mobileMenuOpen, setMobileMenuOpen] = useState(false)
  const [desktopMenuOpen, setDesktopMenuOpen] = useState(false)

  useEffect(() => {
    const unsubscribe = onAuthStateChanged(auth, (currentUser) => {
      setUser(currentUser)
    })

    return () => unsubscribe()
  }, [])

  const handleLogout = async () => {
    try {
      await signOut(auth)
    } catch (error) {
      console.error("Error signing out: ", error)
    }
  }

  return (
    <header className="absolute top-2 md:top-6 w-full z-30">
      <div className="px-4 sm:px-6">
        <div className="max-w-3xl mx-auto">
          <div className="flex items-center justify-between h-14 border border-transparent [background:linear-gradient(theme(colors.white),theme(colors.white))_padding-box,linear-gradient(120deg,theme(colors.zinc.300),theme(colors.zinc.100),theme(colors.zinc.300))_border-box] rounded-lg px-3">
            {/* Logo */}
            <div className="shrink-0 mr-4">
              <Link
                className="flex items-center justify-center bg-white w-8 h-8 rounded shadow-sm shadow-zinc-950/20"
                href="/"
              >
                <Image src={Logo || "/placeholder.svg"} width={24} height={24} alt="Logo" />
              </Link>
            </div>

            {/* Search Box */}
            <div className="flex-grow max-w-md mx-4">
              <SearchBox 
                className="w-full" 
                placeholder="Buscar por título..." 
              />
            </div>

            {/* Mobile menu button */}
            <button 
              className="md:hidden ml-auto mr-2"
              onClick={() => setMobileMenuOpen(!mobileMenuOpen)}
            >
              {mobileMenuOpen ? (
                <X className="h-6 w-6 text-zinc-500" />
              ) : (
                <Menu className="h-6 w-6 text-zinc-500" />
              )}
            </button>

            {/* Desktop Navigation */}
            <nav className="hidden md:flex grow">
              <ul className="flex grow justify-end flex-wrap items-center">
                {/* 📚 Botón Publicar con gradiente fondo completo */}
                <li>
                  <Link
                    href={user ? "/publicar-libro" : "/login"}
                    className="btn-sm text-white font-semibold px-4 py-2 rounded shadow bg-gradient-to-r from-yellow-400 via-red-500 to-purple-400 hover:from-yellow-300 hover:via-red-400 hover:to-purple-300 transition mr-2"
                  >
                    📚 Publicar
                  </Link>
                </li>

                {/* Libros Link */}
                <li>
                  <Link
                    className="block text-sm font-medium text-zinc-500 hover:text-zinc-900 hover:bg-zinc-50 px-4 py-2 transition"
                    href="/books"
                  >
                    Libros
                  </Link>
                </li>
                  
                <li className="relative">
                  <button
                    onClick={() => setDesktopMenuOpen(!desktopMenuOpen)}
                    className="text-sm font-medium text-zinc-500 hover:text-zinc-900 px-3 lg:px-5 py-2 flex items-center transition"
                  >
                    {user ? 'Mi Cuenta' : 'Acceder'} <ChevronDown className="ml-1 h-4 w-4" />
                  </button>
                  
                  {/* Desktop Dropdown Menu */}
                  {desktopMenuOpen && (
                    <div className="absolute top-full right-0 mt-1 w-48 bg-white rounded-lg shadow-lg border border-zinc-200 py-2">
                      {user ? (
                        <>
                          <Link
                            className="block text-sm font-medium text-zinc-500 hover:text-zinc-900 hover:bg-zinc-50 px-4 py-2 transition"
                            href="/mis-libros"
                            onClick={() => setDesktopMenuOpen(false)}
                          >
                            <BookOpen className="w-4 h-4 inline-block mr-2" />
                            Mis Libros
                          </Link>
                          <Link
                            className="block text-sm font-medium text-zinc-500 hover:text-zinc-900 hover:bg-zinc-50 px-4 py-2 transition"
                            href="/mis-chats"
                            onClick={() => setDesktopMenuOpen(false)}
                          >
                            💬 Mis Chats
                          </Link>
                          <div className="border-t border-zinc-200 my-2"></div>
                          <Link
                            className="block text-sm font-medium text-zinc-500 hover:text-zinc-900 hover:bg-zinc-50 px-4 py-2 transition"
                            href="/profile"
                            onClick={() => setDesktopMenuOpen(false)}
                          >
                            <UserCircle className="w-4 h-4 inline-block mr-2" />
                            Mi Perfil
                          </Link>
                          <button
                            className="w-full text-left text-sm font-medium text-zinc-500 hover:text-zinc-900 hover:bg-zinc-50 px-4 py-2 transition"
                            onClick={() => {
                              handleLogout();
                              setDesktopMenuOpen(false);
                            }}
                          >
                            Cerrar Sesión
                          </button>
                        </>
                      ) : (
                        <>
                          <Link
                            className="block text-sm font-medium text-zinc-500 hover:text-zinc-900 hover:bg-zinc-50 px-4 py-2 transition"
                            href="/login"
                            onClick={() => setDesktopMenuOpen(false)}
                          >
                            Log in
                          </Link>
                          <Link
                            className="block text-sm font-medium text-zinc-500 hover:text-zinc-900 hover:bg-zinc-50 px-4 py-2 transition"
                            href="/register"
                            onClick={() => setDesktopMenuOpen(false)}
                          >
                            Register
                          </Link>
                        </>
                      )}
                    </div>
                  )}
                </li>
              </ul>
            </nav>
          </div>

          {/* Mobile menu */}
          {mobileMenuOpen && (
            <div className="md:hidden mt-2 border border-transparent [background:linear-gradient(theme(colors.white),theme(colors.white))_padding-box,linear-gradient(120deg,theme(colors.zinc.300),theme(colors.zinc.100),theme(colors.zinc.300))_border-box] rounded-lg p-3 shadow-lg">
              <nav className="flex flex-col">
                <ul className="space-y-2 mb-4">
                  <li>
                    <Link
                      className="text-sm font-medium text-zinc-500 hover:text-zinc-900 px-3 py-2 flex items-center transition"
                      href="/books"
                      onClick={() => setMobileMenuOpen(false)}
                    >
                      Libros
                    </Link>
                  </li>
                  {user && (
                    <>
                      <li>
                        <Link
                          className="text-sm font-medium text-zinc-500 hover:text-zinc-900 px-3 py-2 flex items-center transition"
                          href="/mis-libros"
                          onClick={() => setMobileMenuOpen(false)}
                        >
                          <BookOpen className="w-4 h-4 mr-1" />
                          Mis Libros
                        </Link>
                      </li>
                      <li>
                        <Link
                          className="text-sm font-medium text-zinc-500 hover:text-zinc-900 px-3 py-2 flex items-center transition"
                          href="/mis-chats"
                          onClick={() => setMobileMenuOpen(false)}
                        >
                          💬 Mis Chats
                        </Link>
                      </li>
                    </>
                  )}
                  <li>
                    <Link
                      href={user ? "/publicar-libro" : "/login"}
                      className="text-sm font-medium text-white px-3 py-2 flex items-center rounded shadow bg-gradient-to-r from-yellow-400 via-red-500 to-purple-400 hover:from-yellow-300 hover:via-red-400 hover:to-purple-300 transition"
                      onClick={() => setMobileMenuOpen(false)}
                    >
                      📚 Publicar
                    </Link>
                  </li>
                </ul>

                <div className="border-t border-zinc-200 pt-2">
                  {user ? (
                    <ul className="space-y-2">
                      <li>
                        <Link
                          href="/profile"
                          className="text-sm font-medium text-zinc-500 hover:text-zinc-900 px-3 py-2 flex items-center transition"
                          onClick={() => setMobileMenuOpen(false)}
                        >
                          <UserCircle className="w-5 h-5 mr-1" />
                          Mi Perfil
                        </Link>
                      </li>
                      <li>
                        <button
                          className="text-sm font-medium text-zinc-100 bg-zinc-900 hover:bg-zinc-800 px-3 py-2 w-full rounded shadow text-left"
                          onClick={() => {
                            handleLogout();
                            setMobileMenuOpen(false);
                          }}
                        >
                          Cerrar Sesión
                        </button>
                      </li>
                    </ul>
                  ) : (
                    <ul className="space-y-2">
                      <li>
                        <Link
                          className="text-sm font-medium text-zinc-500 hover:text-zinc-900 px-3 py-2 flex items-center transition"
                          href="/login"
                          onClick={() => setMobileMenuOpen(false)}
                        >
                          Log in
                        </Link>
                      </li>
                      <li>
                        <Link
                          className="text-sm font-medium text-zinc-100 bg-zinc-900 hover:bg-zinc-800 px-3 py-2 flex items-center rounded shadow"
                          href="/register"
                          onClick={() => setMobileMenuOpen(false)}
                        >
                          Register
                        </Link>
                      </li>
                    </ul>
                  )}
                </div>
              </nav>
            </div>
          )}
        </div>
      </div>
    </header>
  )
}