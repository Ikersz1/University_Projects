import BooksList from "@/components/books/BooksList";
import Header from "@/components/ui/header";

export const metadata = {
  title: 'Libros | BookMatch',
  description: 'Explora nuestra colección de libros',
}

export default function BooksPage() {
  return (
    <>
      <Header />
      <main className="grow">
        <section className="relative">
          <div className="max-w-6xl mx-auto px-4 sm:px-6">
            <div className="pt-32 pb-12 md:pt-40 md:pb-20">
              <div className="max-w-3xl mx-auto text-center pb-12 md:pb-16">
                <h1 className="font-inter-tight text-4xl md:text-5xl font-bold bg-clip-text text-transparent bg-gradient-to-r from-zinc-500 via-zinc-900 to-zinc-900">Libros Disponibles</h1>
                <p className="text-lg text-zinc-500 mt-4">Explora nuestra colección de libros y encuentra tu próxima lectura</p>
              </div>
              <BooksList />
            </div>
          </div>
        </section>
      </main>
    </>
  );
}