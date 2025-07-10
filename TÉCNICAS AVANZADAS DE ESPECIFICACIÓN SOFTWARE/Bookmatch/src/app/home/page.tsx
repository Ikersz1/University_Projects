export const metadata = {
  title: "BookMatch - Intercambia Libros y Conéctate con Lectores",
  description:
    "Descubre BookMatch, la plataforma donde los amantes de la lectura intercambian libros fácilmente. Desliza, haz match y consigue tu próxima gran lectura. ¡Únete ahora!",
}

import NewGallery from "@/components/new-gallery"
import type { NewGalleryItemProps } from "@/components/new-gallery-item"
import NewHero from "@/components/new-hero"
import NewStats from "@/components/new-stats"
import AboutSection from "@/components/about-section"

import { db } from "../../firebaseConfig"
import type Book from "@/bookmatch/book/domain/book.entity"
import BookMostPopularSearcher from "@/bookmatch/book/application/book-most-popular-searcher.use-case"
import FirebaseBookRepository from "@/bookmatch/book/infrastructure/persistence/firebase-book.repository"
import NewTestimonials from "@/components/new-testimonials"

const bookRepository = new FirebaseBookRepository(db)
const searcher = new BookMostPopularSearcher(bookRepository)

const books = await searcher.search(15)

const booksA: NewGalleryItemProps[] = books.map((book: Book) => {
  return {
    id: book.getId().getValue(),
    image: book.getImageUrl(),
  }
})

export default function Home() {
  return (
    <>
      <NewHero />
      <NewStats />
      <NewTestimonials />
      <NewGallery title="🔥LOS MÁS BUSCADOS🔥" items={booksA} />
      <AboutSection />
    </>
  )
}
