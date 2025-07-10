import Book from "@/bookmatch/book/domain/book.entity";
import BookId from "@/bookmatch/book/domain/book-id.value-object";
import UserId from "@/bookmatch/user/domain/user-id.value-object";

interface BookRepository
{
    findById(id: BookId): Promise<Book|null>;

    findByUserId(id: UserId): Promise<Book[]>;

    searchAll(): Promise<Book[]>;

    searchAllPopular(limit: number): Promise<Book[]>;

    save(book: Book): Promise<void>;

    update(book: Book): Promise<void>;

    delete(book: Book): Promise<void>;
}

export default BookRepository;
