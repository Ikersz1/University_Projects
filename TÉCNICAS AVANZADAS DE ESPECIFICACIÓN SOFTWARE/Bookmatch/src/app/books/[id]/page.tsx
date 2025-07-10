import BookDetail from "@/components/books/BookDetail";
import Header from "@/components/ui/header";

type PageProps = {
  params: Promise<{ id: string }>;
};

export default async function BookDetailPage({ params }: PageProps) {
  const { id } = await params;

  return (
    <>
      <Header />
      <main className="grow">
        <section className="relative">
          <div className="max-w-6xl mx-auto px-4 sm:px-6">
            <div className="pt-32 pb-12 md:pt-40 md:pb-20">
              <BookDetail bookId={id} />
            </div>
          </div>
        </section>
      </main>
    </>
  );
}