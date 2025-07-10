"use client"

import type React from "react"

import { useState } from "react"
import { signInWithEmailAndPassword } from "firebase/auth"
import { auth } from "../../firebaseConfig" 
import Link from "next/link"
import { useRouter } from "next/navigation" 

export default function Login() {
  const [email, setEmail] = useState("")
  const [password, setPassword] = useState("")
  const [error, setError] = useState("")
  const [loading, setLoading] = useState(false) 
  const router = useRouter() 

  const handleLogin = async (e: React.FormEvent) => {
    e.preventDefault()
    setError("")
    setLoading(true) 

    try {
      await signInWithEmailAndPassword(auth, email, password)
      router.push("/profile")
    } catch (err) {
      if (err instanceof Error) {
        if (err.message.includes("auth/invalid-credential")) {
          setError("Credenciales incorrectas. Por favor, verifica tu email y contraseña.")
        } else if (err.message.includes("auth/user-not-found")) {
          setError("No existe una cuenta con este email.")
        } else if (err.message.includes("auth/wrong-password")) {
          setError("Contraseña incorrecta.")
        } else {
          setError(err.message)
        }
      } else {
        setError("Ha ocurrido un error inesperado. Por favor, inténtalo de nuevo.")
      }
    } finally {
      setLoading(false) 
    }
  }

  return (
    <>
      {/* Page header */}
      <div className="max-w-3xl mx-auto text-center pb-12 md:pb-16">
        <h1 className="font-inter-tight text-4xl md:text-5xl font-bold bg-clip-text text-transparent bg-gradient-to-r from-zinc-500 via-zinc-900 to-zinc-900">
          Iniciar Sesión
        </h1>
      </div>

      {/* Form */}
      <div className="max-w-[25rem] mx-auto p-6 rounded-lg shadow-2xl bg-gradient-to-b from-zinc-100 to-zinc-50/70 relative before:absolute before:-top-12 before:-left-16 before:w-96 before:h-96 before:bg-zinc-900 before:opacity-[.15] before:rounded-full before:blur-3xl before:-z-10">
        <form onSubmit={handleLogin}>
          <div className="space-y-4">
            <div>
              <label className="block text-sm text-zinc-800 font-medium mb-2" htmlFor="email">
                Email
              </label>
              <input
                id="email"
                className="form-input text-sm w-full"
                type="email"
                placeholder="tu@email.com"
                value={email}
                onChange={(e) => setEmail(e.target.value)}
                required
                disabled={loading}
              />
            </div>
            <div>
              <div className="flex justify-between">
                <label className="block text-sm text-zinc-800 font-medium mb-2" htmlFor="password">
                  Contraseña
                </label>
                <Link
                  className="text-sm font-medium text-zinc-500 underline hover:no-underline ml-2"
                  href="/reset-password"
                >
                  ¿Has olvidado la contraseña?
                </Link>
              </div>
              <input
                id="password"
                className="form-input text-sm w-full"
                type="password"
                value={password}
                onChange={(e) => setPassword(e.target.value)}
                required
                disabled={loading}
              />
            </div>
          </div>
          {error && <p className="text-red-500 text-sm mt-2">{error}</p>}
          <div className="mt-5">
            <button className="btn text-zinc-100 bg-zinc-900 hover:bg-zinc-800 w-full shadow" disabled={loading}>
              {loading ? "Iniciando sesión..." : "Iniciar Sesión"}
            </button>
          </div>
        </form>

        {/* Divider */}
        <div className="flex items-center my-5 before:border-t before:border-zinc-200 before:grow before:mr-3 after:border-t after:border-zinc-200 after:grow after:ml-3">
          <div className="text-xs text-zinc-400 italic">O</div>
        </div>

        {/* Social login */}
        <button
          className="btn text-zinc-600 bg-white hover:text-zinc-900 w-full shadow group relative flex after:flex-1"
          disabled={loading}
        >
          <div className="flex-1 flex items-center">
            <svg
              className="w-4 h-4 fill-zinc-400 group-hover:fill-rose-500 shrink-0 transition"
              viewBox="0 0 16 16"
              xmlns="http://www.w3.org/2000/svg"
            >
              <path d="M15.679 6.545H8.043v3.273h4.328c-.692 2.182-2.401 2.91-4.363 2.91a4.727 4.727 0 1 1 3.035-8.347l2.378-2.265A8 8 0 1 0 8.008 16c4.41 0 8.4-2.909 7.67-9.455Z" />
            </svg>
          </div>
          <span className="flex-auto pl-3">Continuar con google</span>
        </button>

        <div className="text-center mt-6">
          <div className="text-xs text-zinc-500">
            Al iniciar sesión, acepta nuestros{" "}
            <a className="underline hover:no-underline" href="#0">
              Términos
            </a>
            .
          </div>
        </div>
      </div>
    </>
  )
}
