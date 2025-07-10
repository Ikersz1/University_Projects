"use client";

import Link from "next/link";
import { useEffect, useState } from "react";
import { getCategoryName } from "../../services/categoryService";
import { db } from "../../firebaseConfig";

import FirebaseBookRepository from "@/bookmatch/book/infrastructure/persistence/firebase-book.repository";
import BookSearcher from "@/bookmatch/book/application/book-searcher.use-case";
import Book from "@/bookmatch/book/domain/book.entity";
import Image from "next/image";

export default function BooksList() {
  const [books, setBooks] = useState<Book[]>([]);
  const [loading, setLoading] = useState(true);
  const [error, setError] = useState<string | null>(null);
  const [categoryNames, setCategoryNames] = useState<Record<string, string>>({});

  useEffect(() => {
    async function fetchBooks() {
      try {
        const repository = new FirebaseBookRepository(db);
        const searcher = new BookSearcher(repository);

        const booksData = await searcher.all() as Book[];
        setBooks(booksData);
      } catch (err) {
        setError("Error al cargar los libros");
        console.error(err);
      } finally {
        setLoading(false);
      }
    }

    fetchBooks();
  }, []);

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
    );
  }

  if (error) {
    return (
      <div className="text-center py-8">
        <h2 className="text-xl font-bold text-red-600 mb-2">{error}</h2>
      </div>
    );
  }

  if (books.length === 0) {
    return (
      <div className="text-center py-8">
        <h2 className="text-xl font-bold mb-2">No hay libros disponibles</h2>
      </div>
    );
  }

  return (
    <div className="grid grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-6">
      {books.map((book) => (
        <div key={book.getId().getValue()} className="bg-white rounded-lg shadow-md overflow-hidden hover:shadow-lg transition-shadow">
          <div className="p-5">
            <Image src={book.getImageUrl()} width={350} height={300} alt="..."></Image>
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
  );
}