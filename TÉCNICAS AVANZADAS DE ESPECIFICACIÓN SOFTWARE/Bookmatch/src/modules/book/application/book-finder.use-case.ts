import Book from "@/bookmatch/book/domain/book.entity";
import BookId from "@/bookmatch/book/domain/book-id.value-object";
import BookRepository from "@/bookmatch/book/domain/book.repository";
import BookNotFoundException from "@/bookmatch/book/application/book-not-found.error";
import DomainBookFinder from "@/bookmatch/book/domain/book-finder.service";
import BookDoesNotExistsError from "@/bookmatch/book/domain/book-does-not-exists.error";

class BookFinder {
    private readonly finder: DomainBookFinder

    public constructor(repository: BookRepository) {
        this.finder = new DomainBookFinder(repository);
    }

    public async byId(id: BookId): Promise<Book> {
        try {
            const book: Book = await this.finder.byId(id);

            return book;
        } catch (err) {
            if (err instanceof BookDoesNotExistsError) {
                throw new BookNotFoundException(`Book not found by its ID ${id.getValue()}`);
            }

            throw err;
        }
    }

    public async find(id: string): Promise<Book | null> {
        try {
            const bookId = new BookId(id);
            const book = await this.finder.byId(bookId);
            return book;
        } catch (error) {
            console.error(error)
            return null;
        }
    }

}

export default BookFinder;
