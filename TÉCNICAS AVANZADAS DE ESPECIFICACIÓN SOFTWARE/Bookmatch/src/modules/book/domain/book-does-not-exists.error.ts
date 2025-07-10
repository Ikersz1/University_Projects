import DomainError from "@/bookmatch/shared/domain/domain.error";
import BookId from "@/bookmatch/book/domain/book-id.value-object";

class BookDoesNotExistsError extends DomainError {
    public constructor(id: BookId) {
        super(`Book with ID ${id.getValue()} does not exists.`);
        this.name = new.target.name;
    }
}

export default BookDoesNotExistsError;
