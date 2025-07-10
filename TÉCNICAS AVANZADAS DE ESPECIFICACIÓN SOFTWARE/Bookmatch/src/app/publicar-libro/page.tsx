import Header from "@/components/ui/header";
import CrearLibroForm from "@/components/libros/crearLibroForm";

export const metadata = {
  title: 'Crear Libro | BookMatch',
  description: 'Añade un nuevo libro a BookMatch',
}

export default function CrearLibroPage() {
  return (
    <>
      <Header />
      <main className="grow">
        <section className="relative">
          <div className="max-w-6xl mx-auto px-4 sm:px-6">
            <div className="pt-32 pb-12 md:pt-40 md:pb-20">
              <div className="max-w-3xl mx-auto text-center pb-12 md:pb-16">
                <h1 className="font-inter-tight text-4xl md:text-5xl font-bold bg-clip-text text-transparent bg-gradient-to-r from-zinc-500 via-zinc-900 to-zinc-900">
                  Añadir un nuevo libro
                </h1>
              </div>
              <CrearLibroForm />
            </div>
          </div>
        </section>
      </main>
    </>
  );
}
