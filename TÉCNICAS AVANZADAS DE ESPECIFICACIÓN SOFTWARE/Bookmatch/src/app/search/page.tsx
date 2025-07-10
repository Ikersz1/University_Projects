"use client"

import { useState, useEffect, Suspense } from 'react'
import { useSearchParams } from 'next/navigation'
import Image from 'next/image'
import Link from 'next/link'
import { db } from "../../firebaseConfig";
import FirebaseBookRepository from "@/bookmatch/book/infrastructure/persistence/firebase-book.repository"
import BookSearcher from "@/bookmatch/book/application/book-searcher.use-case";
import Book from "@/bookmatch/book/domain/book.entity"
import { getCategoryName } from "../../services/categoryService";
import Header from '@/components/ui/header'

function SearchPage() {
  const searchParams = useSearchParams()
  const searchQuery = searchParams.get('q') || ''
  const [books, setBooks] = useState<Book[]>([])
  const [loading, setLoading] = useState(true)
  const [error, setError] = useState<string | null>(null)
  const [categoryNames, setCategoryNames] = useState<Record<string, string>>({})

  useEffect(() => {
    async function searchBooks() {
      if (!searchQuery.trim()) {
        setBooks([])
        setLoading(false)
        return
      }

      setLoading(true)
      try {
        const repository = new FirebaseBookRepository(db)
        const searcher = new BookSearcher(repository)
  
        const searchResults = await searcher.searchByTitle(searchQuery)
        setBooks(searchResults)
      } catch (err) {
        console.error('Error buscando libros:', err)
        setError('No se pudieron cargar los resultados de búsqueda')
      } finally {
        setLoading(false)
      }
    }

    searchBooks()
  }, [searchQuery])

  useEffect(() => {
    async function fetchCategoryNames() {
      const categoryMap: Record<string, string> = {};

      for (const book of books) {
        if (book.getCategory() && !categoryMap[book.getCategory()]) {
          try {
            const name = await getCategoryName(book.getCategory());
            categoryMap[book.getCategory()] = name || "Categoría sin nombre";
          } catch (error) {
            console.error(`Error fetching category for ${book.getCategory()}:`, error);
            categoryMap[book.getCategory()] = "Categoría desconocida";
          }
        }
      }

      setCategoryNames(categoryMap);
    }

    if (books.length > 0) {
      fetchCategoryNames();
    }
  }, [books]);

  const getFieldValue = (value: string | undefined, fallback: string) => {
    if (!value || value.trim() === '') return fallback;
    return value;
  };

  if (loading) {
    return (
      <>
        <Header />
        <div className="flex justify-center items-center min-h-screen">
          <div className="animate-spin rounded-full h-12 w-12 border-t-2 border-b-2 border-zinc-900"></div>
        </div>
      </>
    )
  }

  if (error) {
    return (
      <>
        <Header />
        <div className="max-w-6xl mx-auto px-4 py-8">
          <div className="bg-red-50 text-red-600 p-4 rounded-lg">
            {error}
          </div>
        </div>
      </>
    )
  }

  return (
    <>
      <Header />
      <div className="max-w-6xl mx-auto px-4 py-8 mt-24">
        <h1 className="text-3xl font-bold mb-6">
          {searchQuery ? `Resultados para "${searchQuery}"` : 'Buscar libros'}
        </h1>

        {books.length > 0 ? (
          <div className="grid grid-cols-1 md:grid-cols-3 lg:grid-cols-4 gap-6">
            {books.map((book) => (
              <div key={book.getId().getValue()} className="bg-white rounded-lg shadow-md overflow-hidden hover:shadow-lg transition-shadow">
                <div className="p-5">
                  <Image 
                    src={book.getImageUrl()} 
                    width={350} 
                    height={300} 
                    alt={book.getTitle() || "Portada del libro"}
                    className="w-full h-auto object-cover"
                  />
                  <h3 className="text-xl font-bold mb-2">
                    {getFieldValue(book.getTitle(), "Título no disponible")}
                  </h3>
                  
                  <div className="flex justify-between items-center mb-4">
                    <span className="text-sm bg-zinc-100 text-zinc-800 px-2 py-1 rounded">
                      {book.getCategory() ? (categoryNames[book.getCategory()] || "Cargando...") : "Sin categoría"}
                    </span>
                    <span className="text-sm text-zinc-500">
                      {getFieldValue(book.getStatus(), "Estado desconocido")}
                    </span>
                  </div>

                  <Link
                    href={`/books/${book.getId().getValue()}`}
                    className="block text-center btn-sm text-zinc-100 bg-zinc-900 hover:bg-zinc-800 w-full shadow"
                  >
                    Ver detalles
                  </Link>
                </div>
              </div>
            ))}
          </div>
        ) : (
          <div className="text-center py-12">
            <p className="text-zinc-600">No se encontraron libros que coincidan con tu búsqueda</p>
            <Link href="/books" className="inline-block mt-4 text-zinc-100 bg-zinc-900 hover:bg-zinc-800 px-6 py-2 rounded">
              Ver todos los libros
            </Link>
          </div>
        )}
      </div>
    </>
  )
}

export default function SuspenseSeachPage() {
  return (
    <Suspense>
      <SearchPage />
    </Suspense>
  )
}