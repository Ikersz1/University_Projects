import BookId from "@/bookmatch/book/domain/book-id.value-object";
import BookNotExistsError from "@/bookmatch/book/domain/book-does-not-exists.error";
import BookRepository from "@/bookmatch/book/domain/book.repository";
import Book from "@/bookmatch/book/domain/book.entity";

class BookFinder
{
    private readonly repository: BookRepository;

    public constructor(repository: BookRepository){
        this.repository = repository;
    }

    public async byId(id: BookId): Promise<Book>{
        const book: Book | null = await this.repository.findById(id);
        if (null === book) {
            throw new BookNotExistsError(id);
        }

        return book;
    }
}

export default BookFinder;
