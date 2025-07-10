"use client";

import { useState } from 'react';
import { sendPasswordResetEmail } from 'firebase/auth';
import { auth } from '../../firebaseConfig';
import Link from 'next/link';

export default function ResetPassword() {
  const [email, setEmail] = useState('');
  const [message, setMessage] = useState('');
  const [error, setError] = useState('');
  const [isLoading, setIsLoading] = useState(false);

  const handleResetPassword = async (e: React.FormEvent) => {
    e.preventDefault();
    setIsLoading(true);
    setMessage('');
    setError('');

    try {
      await sendPasswordResetEmail(auth, email);
      setMessage('Se ha enviado un correo electrónico con instrucciones para restablecer tu contraseña.');
    } catch (err) {
      if (err instanceof Error) {
        setError(err.message);
      } else {
        setError('Se produjo un error al intentar restablecer la contraseña.');
      }
    } finally {
      setIsLoading(false);
    }
  };

  return (
    <>
      {/* Page header */}
      <div className="mt-32"></div> {/* Espacio visual agregado */}

      <div className="max-w-3xl mx-auto text-center pb-12 md:pb-16">
        <h1 className="font-inter-tight text-4xl md:text-5xl font-bold bg-clip-text text-transparent bg-gradient-to-r from-zinc-500 via-zinc-900 to-zinc-900">Resetear Contraseña</h1>
        <p className="text-zinc-600 mt-4">
          No te preocupes, esto suele ocurrir. Introduce la dirección de correo electrónico asociada a tu cuenta.
        </p>
      </div>

      {/* Form */}
      <div className="max-w-[25rem] mx-auto p-6 rounded-lg shadow-2xl bg-gradient-to-b from-zinc-100 to-zinc-50/70 relative before:absolute before:-top-12 before:-left-16 before:w-96 before:h-96 before:bg-zinc-900 before:opacity-[.15] before:rounded-full before:blur-3xl before:-z-10">
        <form onSubmit={handleResetPassword}>
          <div className="space-y-4">
            <div>
              <label className="block text-sm text-zinc-800 font-medium mb-2" htmlFor="email">Email</label>
              <input 
                id="email" 
                className="form-input text-sm w-full" 
                type="email" 
                placeholder="tu@email.com" 
                value={email}
                onChange={(e) => setEmail(e.target.value)}
                required 
              />
            </div>
          </div>
          {message && <p className="text-green-600 text-sm mt-3">{message}</p>}
          {error && <p className="text-red-500 text-sm mt-3">{error}</p>}
          <div className="mt-5">
            <button 
              type="submit" 
              className="btn text-zinc-100 bg-zinc-900 hover:bg-zinc-800 w-full shadow"
              disabled={isLoading}
            >
              {isLoading ? 'Procesando...' : 'Restablecer Contraseña'}
            </button>
          </div>
          <div className="mt-4 text-center">
            <Link href="/login" className="text-sm text-zinc-600 hover:text-zinc-900">
              Volver a iniciar sesión
            </Link>
          </div>
        </form>
      </div>
      <div className="mt-32"></div> {/* Espacio visual agregado */}
    </>
  );
}
