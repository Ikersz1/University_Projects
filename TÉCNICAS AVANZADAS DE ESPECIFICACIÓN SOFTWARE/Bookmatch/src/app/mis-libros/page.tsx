"use client";

import { useState, useEffect } from 'react';
import { collection, query, where, getDocs, deleteDoc, doc } from 'firebase/firestore';
import { onAuthStateChanged } from 'firebase/auth';
import { auth } from '../../firebaseConfig';
import { db } from '../../firebaseConfig';
import Link from 'next/link';
import { getCategoryName } from '../../services/categoryService';

interface Book {
  id: string;
  titulo: string;
  autor: string;
  portada: string;
  descripcion: string;
  propietario: string;
  categoria: string;
  estado: string;
}

export default function MisLibrosPage() {
  const [books, setBooks] = useState<Book[]>([]);
  const [loading, setLoading] = useState(true);
  const [error, setError] = useState<string | null>(null);
  const [categoryNames, setCategoryNames] = useState<Record<string, string>>({});
  const [bookToDelete, setBookToDelete] = useState<string | null>(null);
  const [isDeleting, setIsDeleting] = useState(false);

  useEffect(() => {
    const unsubscribe = onAuthStateChanged(auth, async (user) => {
      if (user) {
        try {
          // Consulta los libros donde el propietario es el ID del usuario actual
          const q = query(
            collection(db, "books"),
            where("propietario", "==", user.uid)
          );
          
          const querySnapshot = await getDocs(q);
          const userBooks: Book[] = [];
          
          querySnapshot.forEach((doc) => {
            userBooks.push({
              id: doc.id,
              ...doc.data() as Omit<Book, 'id'>
            });
          });
          
          setBooks(userBooks);
        } catch (err) {
          console.error("Error al obtener los libros:", err);
          setError("No se pudieron cargar tus libros. Por favor, intenta de nuevo más tarde.");
        } finally {
          setLoading(false);
        }
      } else {
        // Usuario no autenticado, redirigir a la página de login
        window.location.href = '/login';
      }
    });

    return () => unsubscribe();
  }, []);

  useEffect(() => {
    async function fetchCategoryNames() {
      const categoryMap: Record<string, string> = {};
      
      for (const book of books) {
        if (book.categoria && !categoryMap[book.categoria]) {
          try {
            const name = await getCategoryName(book.categoria);
            categoryMap[book.categoria] = name;
          } catch (error) {
            console.error(`Error fetching category for ${book.categoria}:`, error);
            categoryMap[book.categoria] = "Categoría desconocida";
          }
        }
      }
      
      setCategoryNames(categoryMap);
    }
    
    if (books.length > 0) {
      fetchCategoryNames();
    }
  }, [books]);

  // Helper function to check if a field is empty or undefined
  const getFieldValue = (value: string | undefined, fallback: string) => {
    if (!value || value.trim() === '') return fallback;
    return value;
  };

  // Función para confirmar la eliminación de un libro
  const confirmDelete = (bookId: string) => {
    setBookToDelete(bookId);
  };

  // Función para cancelar la eliminación
  const cancelDelete = () => {
    setBookToDelete(null);
  };

  // Función para eliminar un libro
  const deleteBook = async () => {
    if (!bookToDelete) return;
    
    setIsDeleting(true);
    try {
      const bookRef = doc(db, "books", bookToDelete);
      await deleteDoc(bookRef);
      
      // Actualizar la lista de libros eliminando el libro borrado
      setBooks(books.filter(book => book.id !== bookToDelete));
      setBookToDelete(null);
    } catch (err) {
      console.error("Error al eliminar el libro:", err);
      setError("No se pudo eliminar el libro. Por favor, intenta de nuevo más tarde.");
    } finally {
      setIsDeleting(false);
    }
  };

  return (
    <>
      <div className="max-w-3xl mx-auto text-center pb-12 md:pb-16">
        <h1 className="font-inter-tight text-4xl md:text-5xl font-bold bg-clip-text text-transparent bg-gradient-to-r from-zinc-500 via-zinc-900 to-zinc-900">
          Mis Libros
        </h1>
        <p className="text-lg text-zinc-500 mt-4">Administra tu colección personal de libros</p>
      </div>

      {/* Modal de confirmación para eliminar */}
      {bookToDelete && (
        <div className="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50">
          <div className="bg-white p-6 rounded-lg shadow-xl max-w-md w-full">
            <h3 className="text-xl font-bold mb-4">Confirmar eliminación</h3>
            <p className="mb-6">¿Estás seguro de que deseas eliminar este libro? Esta acción no se puede deshacer.</p>
            <div className="flex justify-end space-x-3">
              <button 
                onClick={cancelDelete}
                className="px-4 py-2 border border-zinc-300 rounded-md hover:bg-zinc-100"
                disabled={isDeleting}
              >
                Cancelar
              </button>
              <button 
                onClick={deleteBook}
                className="px-4 py-2 bg-red-600 text-white rounded-md hover:bg-red-700"
                disabled={isDeleting}
              >
                {isDeleting ? "Eliminando..." : "Eliminar"}
              </button>
            </div>
          </div>
        </div>
      )}

      {loading ? (
        <div className="flex justify-center items-center min-h-[400px]">
          <div className="animate-spin rounded-full h-12 w-12 border-t-2 border-b-2 border-zinc-900"></div>
        </div>
      ) : error ? (
        <div className="max-w-3xl mx-auto text-center">
          <p className="text-red-500">{error}</p>
          <button 
            onClick={() => window.location.reload()} 
            className="mt-4 btn text-zinc-100 bg-zinc-900 hover:bg-zinc-800 shadow"
          >
            Intentar de nuevo
          </button>
        </div>
      ) : books.length === 0 ? (
        <div className="max-w-3xl mx-auto text-center">
          <p className="text-zinc-600 mb-6">Aún no has publicado ningún libro.</p>
          <Link 
            href="/publicar-libro" 
            className="btn text-zinc-100 bg-zinc-900 hover:bg-zinc-800 shadow"
          >
            Publicar mi primer libro
          </Link>
        </div>
      ) : (
        <div className="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6 max-w-6xl mx-auto">
          {books.map((book) => (
            <div key={book.id} className="bg-white rounded-lg shadow-md overflow-hidden hover:shadow-lg transition-shadow">
              <div className="p-5">
                <h3 className="text-xl font-bold mb-2">
                  {getFieldValue(book.titulo, "Título no disponible")}
                </h3>
                
                <div className="flex justify-between items-center mb-4">
                  <span className="text-sm bg-zinc-100 text-zinc-800 px-2 py-1 rounded">
                    {book.categoria ? (categoryNames[book.categoria] || "Cargando...") : "Sin categoría"}
                  </span>
                  <span className="text-sm text-zinc-500">
                    {getFieldValue(book.estado, "Estado desconocido")}
                  </span>
                </div>
                
                <div className="flex flex-col space-y-2">
                  <Link 
                    href={`/books/${book.id}`}
                    className="block text-center btn-sm text-zinc-100 bg-zinc-900 hover:bg-zinc-800 w-full shadow"
                  >
                    Ver detalles
                  </Link>

                  <Link 
                    href={`/editar-libro/${book.id}`}
                    className="block text-center btn-sm text-white bg-blue-600 hover:bg-blue-500 w-full shadow"
                  >
                    Editar
                  </Link>
                  
                  <button 
                    onClick={() => confirmDelete(book.id)}
                    className="btn-sm text-zinc-100 bg-red-600 hover:bg-red-700 w-full shadow"
                  >
                    Eliminar
                  </button>
                </div>

              </div>
            </div>
          ))}
        </div>
      )}
    </>
  );
}