import BookRepository from "@/bookmatch/book/domain/book.repository";
import Book from "@/bookmatch/book/domain/book.entity";
import BookId from "@/bookmatch/book/domain/book-id.value-object";
import UserId from "@/bookmatch/user/domain/user-id.value-object";

class BookPublisher {
  private readonly repository: BookRepository;

  public constructor(repository: BookRepository) {
    this.repository = repository;
  }

  public async publish(
    ownerId: UserId,
    title: string,
    description: string,
    category: string,
    status: string,
    isbn: string,
    image: File,
  ): Promise<void> {
    const imageBase64Encoded = await this.fileToBase64(image);

    const book: Book = new Book(
      BookId.random(),
      ownerId,
      title,
      description,
      category,
      status,
      isbn,
      imageBase64Encoded,
    );

    this.repository.save(book);
  }

  public async update(
    bookId: string,
    ownerId: UserId,
    title: string,
    description: string,
    category: string,
    status: string,
    isbn: string,
    image?: File | null
  ): Promise<void> {
    let imageBase64Encoded: string | undefined;
  
    if (image) {
      imageBase64Encoded = await this.fileToBase64(image);
    }
  
    const book: Book = new Book(
      new BookId(bookId),
      ownerId,
      title,
      description,
      category,
      status,
      isbn,
      imageBase64Encoded ?? "" // solo cambia si hay nueva imagen
    );
  
    await this.repository.update(book);
  }
  

  private async fileToBase64(file: File): Promise<string> {
    return new Promise<string>((resolve, reject) => {
      const reader = new FileReader();
      reader.onloadend = () => {
        resolve(reader.result as string);
      };
      reader.onerror = reject;
      reader.readAsDataURL(file);
    });
  }
}

export default BookPublisher;
