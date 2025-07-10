"use client";

import { useEffect, useState } from "react";
import { getUserInfo } from "../../services/userService";
import { getCategoryName } from "../../services/categoryService";
import Link from "next/link";
import { db, auth } from "../../firebaseConfig";
import { collection, addDoc, query, where, getDocs, doc, getDoc, updateDoc, arrayUnion, arrayRemove, setDoc } from "firebase/firestore";

import FirebaseBookRepository from "@/bookmatch/book/infrastructure/persistence/firebase-book.repository"
import BookFinder from "@/bookmatch/book/application/book-finder.use-case"
import BookId from "@/bookmatch/book/domain/book-id.value-object"
import type Book from "@/bookmatch/book/domain/book.entity"
import Image from "next/image"

import { useRouter } from "next/navigation";


interface BookDetailProps {
  bookId: string
}

interface OwnerInfo {
  name: string
  email: string
}

interface UserBook {
  id: string
  title: string
  imageUrl?: string
}

// Añadir esta función auxiliar para formatear fechas sin date-fns
function formatDate(date: Date): string {
  return date.toISOString().split("T")[0] // Formato YYYY-MM-DD
}

function formatTime(date: Date): string {
  return date.toTimeString().split(" ")[0] // Formato HH:MM:SS
}

export default function BookDetail({ bookId }: BookDetailProps) {
  // All state declarations at the top (already correct)
  const [book, setBook] = useState<Book | null>(null);
  const [ownerInfo, setOwnerInfo] = useState<OwnerInfo>({ name: "Cargando...", email: "" });
  const [categoryName, setCategoryName] = useState<string>("Cargando categoría...");
  const [loading, setLoading] = useState(true);
  const [error, setError] = useState<string | null>(null);
  const [isOwner, setIsOwner] = useState(false);
  
  // Match functionality state
  const [showMatchDropdown, setShowMatchDropdown] = useState(false);
  const [userBooks, setUserBooks] = useState<UserBook[]>([]);
  const [loadingBooks, setLoadingBooks] = useState(false);
  const [matchRequestSent, setMatchRequestSent] = useState(false);
  const [matchRequestError, setMatchRequestError] = useState<string | null>(null);

  const router = useRouter();
  
  // Favorite functionality state
  const [isFavorite, setIsFavorite] = useState(false);
  const [checkingFavorite, setCheckingFavorite] = useState(true);
  const [favoriteActionLoading, setFavoriteActionLoading] = useState(false);
  
  // Book data fetching effect
  useEffect(() => {
    async function fetchBook() {
      try {
        const repository = new FirebaseBookRepository(db);
        const finder = new BookFinder(repository);
        
        const bookData = (await finder.byId(new BookId(bookId))) as Book
        console.log("Datos del libro recibidos:", bookData)
        setBook(bookData)

        // Verificar si el usuario actual es el propietario del libro
        const currentUser = auth.currentUser
        if (currentUser && bookData?.getOwnerId().getValue() === currentUser.uid) {
          setIsOwner(true)
        }

        // If we have a book with an owner, fetch the owner's information
        if (bookData.getOwnerId()) {
          const userInfo = await getUserInfo(bookData.getOwnerId().getValue())
          setOwnerInfo({
            name: userInfo.name || "Usuario desconocido",
            email: userInfo.email || "Email no disponible",
          })
        }

        // Fetch the category name if we have a category ID
        if (bookData.getCategory()) {
          const name = await getCategoryName(bookData.getCategory())
          setCategoryName(name)
        } else {
          setCategoryName("Sin categoría")
        }
      } catch (err) {
        setError("Error al cargar los detalles del libro")
        console.error(err)
      } finally {
        setLoading(false)
      }
    }

    fetchBook()
  }, [bookId])

  // Función para cargar los libros del usuario actual
  const loadUserBooks = async () => {
    if (!auth.currentUser) return

    setLoadingBooks(true)
    try {
      const q = query(collection(db, "books"), where("propietario", "==", auth.currentUser.uid))

      const querySnapshot = await getDocs(q)
      const books: UserBook[] = []

      querySnapshot.forEach((doc) => {
        const data = doc.data()
        // No incluir el libro actual en la lista
        if (doc.id !== bookId) {
          books.push({
            id: doc.id,
            title: data.titulo || "Libro sin título",
            imageUrl: data.imageUrl,
          })
        }
      })

      setUserBooks(books)
    } catch (err) {
      console.error("Error al cargar los libros del usuario:", err)
    } finally {
      setLoadingBooks(false)
    }
  }

  // Función para manejar el clic en el botón Match
  const handleMatchClick = () => {
    if (!showMatchDropdown) {
      loadUserBooks()
    }
    setShowMatchDropdown(!showMatchDropdown)
  }

  // Modificar la función sendMatchRequest para usar las nuevas funciones de formato
  const sendMatchRequest = async (offeredBookId: string) => {
    if (!auth.currentUser || !book) return

    try {
      const now = new Date()

      await addDoc(collection(db, "requests"), {
        fecha: formatDate(now),
        hora: formatTime(now),
        libroSolicitado: bookId,
        libroOfrecido: offeredBookId,
        usuarioSolicitante: auth.currentUser.uid,
        usuarioReceptor: book.getOwnerId().getValue(),
        rechazado: false,
        createdAt: now,
      })

      setMatchRequestSent(true)
      setShowMatchDropdown(false)

      // Mostrar mensaje de éxito durante 3 segundos
      setTimeout(() => {
        setMatchRequestSent(false)
      }, 3000)
    } catch (err) {
      console.error("Error al enviar la solicitud de intercambio:", err)
      setMatchRequestError("Error al enviar la solicitud de intercambio")

      // Ocultar mensaje de error después de 3 segundos
      setTimeout(() => {
        setMatchRequestError(null)
      }, 3000)
    }
  }

  // Add favorite checking effect
  useEffect(() => {
    async function checkIfFavorite() {
      const currentUser = auth.currentUser;
      if (!currentUser) {
        setCheckingFavorite(false);
        return;
      }

      try {
        const userDoc = await getDoc(doc(db, "users", currentUser.uid));
        if (userDoc.exists()) {
          const userData = userDoc.data();
          const favorites = userData.favorites || [];
          setIsFavorite(favorites.includes(bookId));
        }
      } catch (err) {
        console.error("Error checking favorites:", err);
      } finally {
        setCheckingFavorite(false);
      }
    }

    checkIfFavorite();
  }, [bookId]);

  // Helper function to check if a field is empty or undefined
  const getFieldValue = (value: string | undefined, fallback: string) => {
    if (!value || value.trim() === '') return fallback;
    return value;
  };

  if (loading) {
    return (
      <div className="flex justify-center items-center min-h-[400px]">
        <div className="animate-spin rounded-full h-12 w-12 border-t-2 border-b-2 border-zinc-900"></div>
      </div>
    )
  }

  if (error || !book) {
    return (
      <div className="text-center py-8">
        <h2 className="text-xl font-bold text-red-600 mb-2">{error || "No se encontró el libro"}</h2>
        <Link href="/books" className="text-blue-600 hover:underline">
          Volver a la lista de libros
        </Link>
      </div>
    )
  }

  // Add function to toggle favorite status
  const toggleFavorite = async () => {
    const currentUser = auth.currentUser;
    if (!currentUser) {
      // Redirect to login or show message
      return;
    }

    setFavoriteActionLoading(true);
    try {
      const userRef = doc(db, "users", currentUser.uid);
      
      if (isFavorite) {
        // Remove from favorites
        await updateDoc(userRef, {
          favorites: arrayRemove(bookId)
        });
        setIsFavorite(false);
      } else {
        // Add to favorites
        await updateDoc(userRef, {
          favorites: arrayUnion(bookId)
        });
        setIsFavorite(true);
      }
    } catch (err) {
      console.error("Error updating favorites:", err);
    } finally {
      setFavoriteActionLoading(false);
    }
  };

  // Conditional returns for loading and error states
  if (loading) {
    return (
      <div className="flex justify-center items-center min-h-[400px]">
        <div className="animate-spin rounded-full h-12 w-12 border-t-2 border-b-2 border-zinc-900"></div>
      </div>
    )
  }

  if (error || !book) {
    return (
      <div className="text-center py-8">
        <h2 className="text-xl font-bold text-red-600 mb-2">{error || "No se encontró el libro"}</h2>
        <Link href="/books" className="text-blue-600 hover:underline">
          Volver a la lista de libros
        </Link>
      </div>
    )
  }
  const handleContactClick = async () => {
    const currentUser = auth.currentUser;
    if (!currentUser || !book) return;
  
    // ID único por conversación: usuario + propietario + libro
    const chatId = `${currentUser.uid}_${book.getOwnerId().getValue()}_${bookId}`;
  
    try {
      const chatRef = doc(db, "chats", chatId);
      const chatSnap = await getDoc(chatRef);
  
      if (!chatSnap.exists()) {
        await setDoc(chatRef, {
          usuarios: [currentUser.uid, book.getOwnerId().getValue()],
          creadoEn: new Date(),
          libro: {
            id: bookId,
            titulo: book.getTitle(),
            imageUrl: book.getImageUrl() || null
          }
        });
      }
  
      // Redirigir a la lista de chats
      router.push("/mis-chats");
    } catch (err) {
      console.error("Error al iniciar o redirigir al chat:", err);
    }
  };
  
    
  return (
    <div className="bg-white rounded-lg shadow-xl overflow-hidden max-w-4xl mx-auto">
      <div className="p-6 md:p-8">
        <h1 className="text-3xl font-bold mb-4">{getFieldValue(book.getTitle(), "Libro sin título")}</h1>

        <div className="grid grid-cols-1 md:grid-cols-3 gap-6 mb-8">
          <div className="md:col-span-2">
            {/* Añadir imagen de portada si está disponible */}
            {book.getImageUrl() && (
              <div className="mb-6">
                <Image
                  src={book.getImageUrl() || "/images/dummy-book-01.jpg"}
                  alt={getFieldValue(book.getTitle(), "Portada de libro")}
                  className="w-full h-auto object-cover rounded-lg"
                  width={500}
                  height={300}
                />
              </div>
            )}

            <div className="mb-6">
              <h2 className="text-xl font-semibold mb-2">Descripción</h2>
              <p className="text-zinc-700">{getFieldValue(book.getDescription(), "Sin descripción disponible")}</p>
            </div>

            {/* Mostrar autor si está disponible */}
            {book.getAuthor() && (
              <div className="mb-6">
                <h2 className="text-xl font-semibold mb-2">Autor</h2>
                <p className="text-zinc-700">{getFieldValue(book.getAuthor(), "Autor desconocido")}</p>
              </div>
            )}

            <div className="grid grid-cols-2 gap-4">
              <div>
                <h3 className="font-medium text-zinc-900">Categoría</h3>
                <p className="text-zinc-600">{categoryName}</p>
              </div>
              <div>
                <h3 className="font-medium text-zinc-900">Estado</h3>
                <p className="text-zinc-600">{getFieldValue(book.getStatus(), "Estado desconocido")}</p>
              </div>
            </div>
          </div>

          <div className="bg-zinc-50 p-4 rounded-lg">
            <h2 className="text-lg font-semibold mb-3">Detalles</h2>

            <div className="space-y-3">
              <div>
                <h3 className="text-sm font-medium text-zinc-500">ISBN</h3>
                <p className="text-zinc-800">{getFieldValue(book.getIsbn(), "ISBN no disponible")}</p>
              </div>

              <div>
                <h3 className="text-sm font-medium text-zinc-500">Propietario</h3>
                <p className="text-zinc-800 font-medium">{ownerInfo.name}</p>
                {ownerInfo.email && <p className="text-zinc-600 text-sm mt-1">{ownerInfo.email}</p>}
              </div>
            </div>
            
            <div className="mt-6 space-y-3">
              {/* Add favorite button */}
              {auth.currentUser && !isOwner && !checkingFavorite && (
                <button 
                  onClick={toggleFavorite}
                  disabled={favoriteActionLoading}
                  className={`btn-sm w-full shadow flex items-center justify-center ${
                    isFavorite 
                      ? "text-red-600 bg-white hover:bg-red-50 border border-red-200" 
                      : "text-zinc-600 bg-white hover:text-zinc-900 border border-zinc-200"
                  }`}
                >
                  <svg 
                    xmlns="http://www.w3.org/2000/svg" 
                    className={`h-5 w-5 mr-1 ${isFavorite ? "fill-red-600" : "fill-none stroke-current"}`} 
                    viewBox="0 0 24 24" 
                    stroke="currentColor" 
                    strokeWidth={isFavorite ? 0 : 2}
                  >
                    <path 
                      strokeLinecap="round" 
                      strokeLinejoin="round" 
                      d="M4.318 6.318a4.5 4.5 0 000 6.364L12 20.364l7.682-7.682a4.5 4.5 0 00-6.364-6.364L12 7.636l-1.318-1.318a4.5 4.5 0 00-6.364 0z" 
                    />
                  </svg>
                  {isFavorite ? "Quitar de favoritos" : "Añadir a favoritos"}
                </button>
              )}
              
              {isOwner ? (
                <Link
                  href={`/editar-libro/${bookId}`}
                  className="btn-sm text-zinc-100 bg-zinc-900 hover:bg-zinc-800 w-full shadow block text-center"
                >
                  Editar libro
                </Link>
              ) : (
                <>
                  {/* Botón de Match */}
                  <div className="relative">
                    <button
                      onClick={handleMatchClick}
                      className="btn-sm text-white font-semibold w-full shadow bg-gradient-to-r from-yellow-400 via-red-500 to-purple-400 hover:from-yellow-300 hover:via-red-400 hover:to-purple-300 transition"
                    >
                      🔄 Proponer Intercambio
                    </button>

                    {/* Dropdown de libros del usuario */}
                    {showMatchDropdown && (
                      <div className="absolute top-full left-0 right-0 mt-2 bg-white rounded-lg shadow-lg z-10 max-h-80 overflow-y-auto">
                        <div className="p-3 border-b border-zinc-200">
                          <h3 className="font-medium text-zinc-900">Selecciona un libro para intercambiar</h3>
                        </div>

                        {loadingBooks ? (
                          <div className="p-4 text-center">
                            <div className="inline-block animate-spin rounded-full h-6 w-6 border-t-2 border-b-2 border-zinc-900"></div>
                            <p className="mt-2 text-sm text-zinc-600">Cargando tus libros...</p>
                          </div>
                        ) : userBooks.length > 0 ? (
                          <ul className="py-2">
                            {userBooks.map((userBook) => (
                              <li key={userBook.id} className="px-3 py-2 hover:bg-zinc-50">
                                <div className="flex items-center justify-between">
                                  <span className="text-zinc-800 truncate pr-2">{userBook.title}</span>
                                  <button
                                    onClick={() => sendMatchRequest(userBook.id)}
                                    className="btn-sm bg-zinc-800 text-white hover:bg-zinc-700 text-xs"
                                  >
                                    Seleccionar
                                  </button>
                                </div>
                              </li>
                            ))}
                          </ul>
                        ) : (
                          <div className="p-4 text-center">
                            <p className="text-sm text-zinc-600">No tienes libros disponibles para intercambiar.</p>
                            <Link
                              href="/publicar-libro"
                              className="text-sm text-blue-600 hover:underline mt-2 inline-block"
                            >
                              Publicar un libro
                            </Link>
                          </div>
                        )}
                      </div>
                    )}
                  </div>

                  {/* Mensaje de éxito o error */}
                  {matchRequestSent && (
                    <div className="bg-green-50 border-l-4 border-green-500 p-3 text-sm text-green-700">
                      Solicitud de intercambio enviada correctamente.
                    </div>
                  )}

                  {matchRequestError && (
                    <div className="bg-red-50 border-l-4 border-red-500 p-3 text-sm text-red-700">
                      {matchRequestError}
                    </div>
                  )}

                  <button
                    onClick={handleContactClick}
                    className="btn-sm text-zinc-100 bg-zinc-900 hover:bg-zinc-800 w-full shadow">
                    Contactar al propietario
                  </button>
                </>
              )}
            </div>
          </div>
        </div>

        <div className="border-t border-zinc-200 pt-4">
          <Link href="/books" className="text-blue-600 hover:underline flex items-center">
            <svg
              xmlns="http://www.w3.org/2000/svg"
              className="h-4 w-4 mr-1"
              fill="none"
              viewBox="0 0 24 24"
              stroke="currentColor"
            >
              <path strokeLinecap="round" strokeLinejoin="round" strokeWidth={2} d="M10 19l-7-7m0 0l7-7m-7 7h18" />
            </svg>
            Volver a la lista de libros
          </Link>
        </div>
      </div>
    </div>
  )
}