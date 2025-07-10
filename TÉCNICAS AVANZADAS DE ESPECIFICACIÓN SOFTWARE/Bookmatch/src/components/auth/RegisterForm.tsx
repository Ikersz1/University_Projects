"use client";

import { useState } from "react";
import { useRouter } from "next/navigation";
import Link from "next/link";
import { createUserWithEmailAndPassword, updateProfile } from "firebase/auth";
import { auth } from "../../firebaseConfig";
import { doc, getFirestore, setDoc } from "firebase/firestore";

export default function RegisterForm() {
  const [name, setName] = useState("");
  const [email, setEmail] = useState("");
  const [password, setPassword] = useState("");
  const [confirmPassword, setConfirmPassword] = useState("");
  const [error, setError] = useState("");
  const [loading, setLoading] = useState(false);
  
  // Instead of using context, we'll implement the register function directly
  const router = useRouter();
  const db = getFirestore();

  const handleSubmit = async (e: React.FormEvent) => {
    e.preventDefault();
    
    // Validaciones básicas
    if (!name || !email || !password || !confirmPassword) {
      setError("Todos los campos son obligatorios");
      return;
    }
    
    if (password !== confirmPassword) {
      setError("Las contraseñas no coinciden");
      return;
    }
    
    if (password.length < 6) {
      setError("La contraseña debe tener al menos 6 caracteres");
      return;
    }
    
    try {
      setError("");
      setLoading(true);
      
      // Register the user directly instead of using context
      const userCredential = await createUserWithEmailAndPassword(auth, email, password);
      
      // Update profile with name
      if (userCredential.user) {
        await updateProfile(userCredential.user, {
          displayName: name
        });
        
        // Save additional user data to Firestore
        await setDoc(doc(db, "users", userCredential.user.uid), {
          name,
          email,
          createdAt: new Date().toISOString(),
          favorites: [],
          readingList: []
        });
      }
      
      router.push("/"); // Redirigir a la página principal después del registro exitoso
    } catch (error: unknown) {
      if (error instanceof Error) {
        if ('code' in error && error.code === "auth/email-already-in-use") {
          setError("Este correo electrónico ya está en uso");
        } else {
          setError("Error al crear la cuenta. Por favor, inténtalo de nuevo.");
          console.error(error);
        }
      } else {
        setError("Error al crear la cuenta. Por favor, inténtalo de nuevo.");
        console.error(error);
      }
    } finally {
      setLoading(false);
    }
  };

  return (
    <div className="max-w-[25rem] mx-auto p-6 rounded-lg shadow-2xl bg-gradient-to-b from-zinc-100 to-zinc-50/70 relative before:absolute before:-top-12 before:-left-16 before:w-96 before:h-96 before:bg-zinc-900 before:opacity-[.15] before:rounded-full before:blur-3xl before:-z-10">
      <h2 className="text-2xl font-bold mb-6 text-center">Crear una cuenta</h2>
      
      {error && (
        <div className="mb-4 p-3 bg-red-100 text-red-700 rounded-md">
          {error}
        </div>
      )}
      
      <form onSubmit={handleSubmit}>
        <div className="space-y-4">
          <div>
            <label className="block text-sm text-zinc-800 font-medium mb-2" htmlFor="name">
              Nombre completo
            </label>
            <input
              type="text"
              id="name"
              value={name}
              onChange={(e) => setName(e.target.value)}
              className="form-input text-sm w-full"
              placeholder="Tu nombre"
            />
          </div>
          
          <div>
            <label className="block text-sm text-zinc-800 font-medium mb-2" htmlFor="email">
              Correo electrónico
            </label>
            <input
              type="email"
              id="email"
              value={email}
              onChange={(e) => setEmail(e.target.value)}
              className="form-input text-sm w-full"
              placeholder="tu@email.com"
            />
          </div>
          
          <div>
            <label className="block text-sm text-zinc-800 font-medium mb-2" htmlFor="password">
              Contraseña
            </label>
            <input
              type="password"
              id="password"
              value={password}
              onChange={(e) => setPassword(e.target.value)}
              className="form-input text-sm w-full"
              placeholder="Mínimo 6 caracteres"
            />
          </div>
          
          <div>
            <label className="block text-sm text-zinc-800 font-medium mb-2" htmlFor="confirmPassword">
              Confirmar contraseña
            </label>
            <input
              type="password"
              id="confirmPassword"
              value={confirmPassword}
              onChange={(e) => setConfirmPassword(e.target.value)}
              className="form-input text-sm w-full"
              placeholder="Repite tu contraseña"
            />
          </div>
        </div>
        
        <div className="mt-5">
          <button
            type="submit"
            disabled={loading}
            className="btn text-zinc-100 bg-zinc-900 hover:bg-zinc-800 w-full shadow"
          >
            {loading ? "Creando cuenta..." : "Registrarse"}
          </button>
        </div>
      </form>
      
      <div className="text-center mt-6">
        <div className="text-sm text-zinc-500">
          ¿Ya tienes una cuenta?{" "}
          <Link href="/login" className="text-blue-600 hover:underline">
            Iniciar sesión
          </Link>
        </div>
      </div>
    </div>
  );
}