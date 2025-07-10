import BookRepository from "@/bookmatch/book/domain/book.repository";
import Book from "@/bookmatch/book/domain/book.entity";

class BookTitleSearcher {
    private readonly repository: BookRepository;

    public constructor(repository: BookRepository) {
        this.repository = repository;
    }

    public async all(): Promise<Book[]> {
        return this.repository.searchAll();
    }

    public async searchByTitle(query: string): Promise<Book[]> {
        const allBooks = await this.repository.searchAll();
        const searchQuery = query.toLowerCase();
        
        return allBooks.filter(book => {
            const titleMatch = book.getTitle().toLowerCase().includes(searchQuery);
            return titleMatch;
        });
    }
}

export default BookTitleSearcher;
