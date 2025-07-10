'use client';

import { useEffect, useState } from "react";
import { useParams, useRouter } from "next/navigation";
import { onAuthStateChanged } from "firebase/auth";
import { auth, db } from "../../../firebaseConfig";

import FirebaseBookRepository from "@/bookmatch/book/infrastructure/persistence/firebase-book.repository";
import BookFinder from "@/bookmatch/book/application/book-finder.use-case";
import EditarLibroForm from "@/components/libros/editarLibroForm";
import Header from "@/components/ui/header";
import Book from "@/bookmatch/book/domain/book.entity";



export default function EditarLibroPage() {
  // Eliminamos la variable user que no se utiliza
  const [libro, setLibro] = useState<Book | null>(null);
  const [loading, setLoading] = useState(true);
  const router = useRouter();
  const params = useParams();
  const id = params.id as string;

  useEffect(() => {
    const unsubscribe = onAuthStateChanged(auth, async (firebaseUser) => {
      if (!firebaseUser) {
        router.push("/login");
        return;
      }

      try {
        const repository = new FirebaseBookRepository(db);
        const finder = new BookFinder(repository);
        const libroEncontrado = await finder.find(id);

        if (!libroEncontrado || libroEncontrado.getOwnerId().getValue() !== firebaseUser.uid) {
          router.push("/unauthorized");
          return;
        }

        setLibro(libroEncontrado);
      } catch (error) {
        console.error("Error cargando libro:", error);
        router.push("/unauthorized");
      } finally {
        setLoading(false);
      }
    });

    return () => unsubscribe();
  }, [id, router]);

  if (loading) return <p className="text-center mt-32">Cargando libro...</p>;
  if (!libro) return <p className="text-center mt-32">No se encontró el libro</p>;

  return (
    <>
      <Header />
      <main className="grow">
        <section className="relative">
          <div className="max-w-6xl mx-auto px-4 sm:px-6">
            <div className="pt-32 pb-12 md:pt-40 md:pb-20">
              <div className="max-w-3xl mx-auto text-center pb-12 md:pb-16">
                <h1 className="font-inter-tight text-4xl md:text-5xl font-bold bg-clip-text text-transparent bg-gradient-to-r from-zinc-500 via-zinc-900 to-zinc-900">
                  Editar libro
                </h1>
              </div>
              <EditarLibroForm libro={libro} />
            </div>
          </div>
        </section>
      </main>
    </>
  );
}