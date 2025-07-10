"use client"

import type React from "react"

import { useState, useEffect } from "react"
import { useRouter } from "next/navigation"
import Link from "next/link" 
import { onAuthStateChanged } from "firebase/auth"
import { getFirestore, doc, getDoc, updateDoc } from "firebase/firestore"
import { auth } from "../../firebaseConfig" 
import Image from "next/image"

import FirebaseBookRepository from "@/bookmatch/book/infrastructure/persistence/firebase-book.repository"
import BookFinder from "@/bookmatch/book/application/book-finder.use-case"
import BookId from "@/bookmatch/book/domain/book-id.value-object"
import Book from "@/bookmatch/book/domain/book.entity"

interface UserProfile {
  nombre: string
  email: string
  direccion: string
  codigoPostal: string
  localidad: string
  telefono: string
  favorites: string[] 
}

export default function ProfilePage() {
  const [user, setUser] = useState<UserProfile | null>(null)
  const [loading, setLoading] = useState(true)
  const [editing, setEditing] = useState(false)
  const [formData, setFormData] = useState<UserProfile | null>(null)
  const [saving, setSaving] = useState(false)
  const [error, setError] = useState<string | null>(null)
  const [success, setSuccess] = useState<string | null>(null)
  const [favoriteBooks, setFavoriteBooks] = useState<Book[]>([]) 
  const [loadingFavorites, setLoadingFavorites] = useState(false)
  const router = useRouter()
  const db = getFirestore()

  const getFieldValue = (value: string | undefined, fallback: string) => {
    if (!value || value.trim() === '') return fallback;
    return value;
  };

  useEffect(() => {
    const unsubscribe = onAuthStateChanged(auth, async (currentUser) => {
      if (currentUser) {
        try {
          const userDoc = await getDoc(doc(db, "users", currentUser.uid))
          if (userDoc.exists()) {
            const userData = userDoc.data() as UserProfile
            setUser(userData)
            setFormData(userData)
          } else {
            setError("No se ha encontrado información de tu perfil")
          }
        } catch (err) {
          console.error("Error fetching user data:", err)
          setError("No se ha podido cargar tu perfil")
        }
      } else {
        router.push("/login")
      }
      setLoading(false)
    })

    return () => unsubscribe()
  }, [router, db])

  useEffect(() => {
    async function fetchFavoriteBooks() {
      if (!user?.favorites || user.favorites.length === 0) {
        setFavoriteBooks([]);
        setLoadingFavorites(false);
        return;
      }
      
      setLoadingFavorites(true);
      try {
        const repository = new FirebaseBookRepository(db);
        const finder = new BookFinder(repository);
        
        const bookPromises = user.favorites.map(async (bookId) => {
          try {
            return await finder.byId(new BookId(bookId));
          } catch (err) {
            console.error(`Error fetching book ${bookId}:`, err);
            return null;
          }
        });
        
        const books = await Promise.all(bookPromises);
        setFavoriteBooks(books.filter(book => book !== null) as Book[]);
      } catch (err) {
        console.error("Error fetching favorite books:", err);
      } finally {
        setLoadingFavorites(false);
      }
    }
    
    if (user) {
      fetchFavoriteBooks();
    }
  }, [user, db]);

  const handleInputChange = (e: React.ChangeEvent<HTMLInputElement>) => {
    const { name, value } = e.target
    console.log(`Changing ${name} to ${value}`) 
    setFormData((prev) => {
      if (!prev) return null
      return {
        ...prev,
        [name]: value,
      }
    })
  }

  const handleSave = async (e: React.FormEvent) => {
    e.preventDefault()
    if (!formData || !auth.currentUser) return

    setSaving(true)
    setError(null)
    setSuccess(null)

    try {
      await updateDoc(doc(db, "users", auth.currentUser.uid), formData as any) // eslint-disable-line
      setUser(formData)
      setEditing(false)
      setSuccess("Tu información ha sido actualizada correctamente")

      setTimeout(() => {
        setSuccess(null)
      }, 3000)
    } catch (err) {
      console.error("Error updating profile:", err)
      setError("No se ha podido actualizar tu perfil")
    } finally {
      setSaving(false)
    }
  }

  const cancelEdit = () => {
    setFormData(user)
    setEditing(false)
    setError(null)
  }

  const enableEditing = () => {
    setFormData({ ...user } as UserProfile)
    setEditing(true)
  }

  const removeFromFavorites = async (bookId: string) => {
    if (!auth.currentUser) return;
    
    try {
      const updatedFavorites = user?.favorites.filter(id => id !== bookId) || [];
      
      await updateDoc(doc(db, "users", auth.currentUser.uid), {
        favorites: updatedFavorites
      });
      
      setUser(prev => prev ? {...prev, favorites: updatedFavorites} : null);
      setFavoriteBooks(prev => prev.filter(book => book.getId().getValue() !== bookId));
      
      setSuccess("Libro eliminado de favoritos");
      setTimeout(() => setSuccess(null), 3000);
    } catch (err) {
      console.error("Error removing from favorites:", err);
      setError("No se pudo eliminar el libro de favoritos");
      setTimeout(() => setError(null), 3000);
    }
  };

  if (loading) {
    return (
      <div className="flex justify-center items-center min-h-[400px]">
        <div className="animate-spin rounded-full h-12 w-12 border-t-2 border-b-2 border-zinc-900"></div>
      </div>
    )
  }

  return (
    <>
      {/* Page header */}
      <div className="max-w-3xl mx-auto text-center pb-12 md:pb-16">
        <h1 className="font-inter-tight text-4xl md:text-5xl font-bold bg-clip-text text-transparent bg-gradient-to-r from-zinc-500 via-zinc-900 to-zinc-900">
          Mi Perfil
        </h1>
      </div>

      {/* Profile content */}
      <div className="max-w-[25rem] mx-auto p-6 rounded-lg shadow-2xl bg-gradient-to-b from-zinc-100 to-zinc-50/70 relative before:absolute before:-top-12 before:-left-16 before:w-96 before:h-96 before:bg-zinc-900 before:opacity-[.15] before:rounded-full before:blur-3xl before:-z-10">
        {error && <p className="text-red-500 text-sm mb-4">{error}</p>}
        {success && <p className="text-green-500 text-sm mb-4">{success}</p>}

        {editing ? (
          <form onSubmit={handleSave}>
            <div className="space-y-4">
              <div>
                <label className="block text-sm text-zinc-800 font-medium mb-2" htmlFor="nombre">
                  Nombre
                </label>
                <input
                  id="nombre"
                  name="nombre"
                  className="form-input text-sm w-full"
                  type="text"
                  value={formData?.nombre || ""}
                  onChange={handleInputChange}
                  required
                />
              </div>
              <div>
                <label className="block text-sm text-zinc-800 font-medium mb-2" htmlFor="email">
                  Email
                </label>
                <input
                  id="email"
                  name="email"
                  className="form-input text-sm w-full bg-zinc-100"
                  type="email"
                  value={formData?.email || ""}
                  onChange={handleInputChange}
                  disabled
                />
              </div>
              <div>
                <label className="block text-sm text-zinc-800 font-medium mb-2" htmlFor="direccion">
                  Dirección
                </label>
                <input
                  id="direccion"
                  name="direccion"
                  className="form-input text-sm w-full"
                  type="text"
                  value={formData?.direccion || ""}
                  onChange={handleInputChange}
                />
              </div>
              <div className="grid grid-cols-2 gap-4">
                <div>
                  <label className="block text-sm text-zinc-800 font-medium mb-2" htmlFor="codigoPostal">
                    Código Postal
                  </label>
                  <input
                    id="codigoPostal"
                    name="codigoPostal"
                    className="form-input text-sm w-full"
                    type="text"
                    value={formData?.codigoPostal || ""}
                    onChange={handleInputChange}
                  />
                </div>
                <div>
                  <label className="block text-sm text-zinc-800 font-medium mb-2" htmlFor="localidad">
                    Localidad
                  </label>
                  <input
                    id="localidad"
                    name="localidad"
                    className="form-input text-sm w-full"
                    type="text"
                    value={formData?.localidad || ""}
                    onChange={handleInputChange}
                  />
                </div>
              </div>
              <div>
                <label className="block text-sm text-zinc-800 font-medium mb-2" htmlFor="telefono">
                  Teléfono
                </label>
                <input
                  id="telefono"
                  name="telefono"
                  className="form-input text-sm w-full"
                  type="tel"
                  value={formData?.telefono || ""}
                  onChange={handleInputChange}
                />
              </div>
            </div>

            <div className="mt-5 space-y-3">
              <button
                type="submit"
                className="btn text-zinc-100 bg-zinc-900 hover:bg-zinc-800 w-full shadow"
                disabled={saving}
              >
                {saving ? "Guardando..." : "Guardar Cambios"}
              </button>
              <button
                type="button"
                className="btn text-zinc-600 bg-white hover:text-zinc-900 w-full shadow"
                onClick={cancelEdit}
                disabled={saving}
              >
                Cancelar
              </button>
            </div>
          </form>
        ) : (
          <div className="space-y-5">
            <div className="border-b border-zinc-200 pb-4">
              <h2 className="text-xl font-semibold text-zinc-900 mb-1">{user?.nombre}</h2>
              <p className="text-zinc-500">{user?.email}</p>
            </div>

            <div className="space-y-4">
              <div>
                <h3 className="text-sm font-medium text-zinc-800 mb-1">Dirección</h3>
                <p className="text-sm text-zinc-600">{user?.direccion || "No especificada"}</p>
                <p className="text-sm text-zinc-600">
                  {user?.codigoPostal} {user?.localidad}
                </p>
              </div>

              <div>
                <h3 className="text-sm font-medium text-zinc-800 mb-1">Teléfono</h3>
                <p className="text-sm text-zinc-600">{user?.telefono || "No especificado"}</p>
              </div>
            </div>

            <div className="space-y-3">
              <button className="btn text-zinc-100 bg-zinc-900 hover:bg-zinc-800 w-full shadow" onClick={enableEditing}>
                Editar Perfil
              </button>
              <Link
                href="/matches"
                className="btn text-white font-semibold w-full shadow bg-gradient-to-r from-yellow-400 via-red-500 to-purple-400 hover:from-yellow-300 hover:via-red-400 hover:to-purple-300 transition flex items-center justify-center"
              >
                🔄 Ver Mis Intercambios
              </Link>
            </div>
          </div>
        )}
      </div>

      {/* Seccion favoritos */}
      <div className="max-w-3xl mx-auto mt-12">
        <h2 className="text-2xl font-bold mb-6">Mis Libros Favoritos</h2>
        
        {loadingFavorites ? (
          <div className="flex justify-center py-8">
            <div className="animate-spin rounded-full h-8 w-8 border-t-2 border-b-2 border-zinc-900"></div>
          </div>
        ) : favoriteBooks.length > 0 ? (
          <div className="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
            {favoriteBooks.map(book => (
              <div key={book.getId().getValue()} className="bg-white rounded-lg shadow-md overflow-hidden hover:shadow-lg transition-shadow">
                <div className="p-5">
                  {book.getImageUrl() && (
                    <Image src={book.getImageUrl()} width={350} height={300} alt={book.getTitle() || "Portada del libro"} className="mb-4 w-full h-auto object-cover" />
                  )}
                  
                  <h3 className="text-xl font-bold mb-2">
                    {getFieldValue(book.getTitle(), "Título no disponible")}
                  </h3>
                  
                  <div className="flex justify-between items-center mb-4">
                    <span className="text-sm bg-zinc-100 text-zinc-800 px-2 py-1 rounded">
                      {book.getCategory() || "Sin categoría"}
                    </span>
                    <span className="text-sm text-zinc-500">
                      {getFieldValue(book.getStatus(), "Estado desconocido")}
                    </span>
                  </div>
                  
                  <div className="flex space-x-2">
                    <Link 
                      href={`/books/${book.getId().getValue()}`}
                      className="flex-1 block text-center btn-sm text-zinc-100 bg-zinc-900 hover:bg-zinc-800 shadow"
                    >
                      Ver detalles
                    </Link>
                    <button
                      onClick={() => removeFromFavorites(book.getId().getValue())}
                      className="btn-sm text-red-600 bg-white hover:bg-red-50 border border-red-200 shadow"
                      title="Eliminar de favoritos"
                    >
                      <svg xmlns="http://www.w3.org/2000/svg" className="h-5 w-5 fill-current" viewBox="0 0 24 24">
                        <path d="M12 21.35l-1.45-1.32C5.4 15.36 2 12.28 2 8.5 2 5.42 4.42 3 7.5 3c1.74 0 3.41.81 4.5 2.09C13.09 3.81 14.76 3 16.5 3 19.58 3 22 5.42 22 8.5c0 3.78-3.4 6.86-8.55 11.54L12 21.35z" />
                      </svg>
                    </button>
                  </div>
                </div>
              </div>
            ))}
          </div>
        ) : (
          <div className="text-center py-8 bg-zinc-50 rounded-lg">
            <p className="text-zinc-600 mb-4">No tienes libros favoritos todavía</p>
            <Link href="/books" className="btn-sm text-zinc-100 bg-zinc-900 hover:bg-zinc-800 shadow">
              Explorar libros
            </Link>
          </div>
        )}
      </div>
    </>
  );
}
