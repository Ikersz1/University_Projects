import Book from "@/bookmatch/book/domain/book.entity";
import BookRepository from "@/bookmatch/book/domain/book.repository";

class BookMostPopularSearcher {
    private readonly repository: BookRepository;

    constructor(repository: BookRepository) {
        this.repository = repository
    }

    public async search(limit: number = 15): Promise<Book[]> {
        return this.repository.searchAllPopular(limit);
    }
}

export default BookMostPopularSearcher;