import BookId from "@/bookmatch/book/domain/book-id.value-object";
import UserId from "@/bookmatch/user/domain/user-id.value-object";

class Book {
  private readonly id: BookId;
  private readonly ownerId: UserId;
  private title: string;
  private description: string;
  private category: string;
  private status: string;
  private isbn: string;
  private imageUrl: string;
  private author: string;
  private modifiedAt: Date;
  private readonly createdAt: Date;

  public constructor(
    id: BookId,
    ownerId: UserId,
    title: string,
    description: string,
    category: string,
    status: string,
    isbn: string,
    imageUrl: string,
    modifiedAt?: Date,
    createdAt?: Date,
  ) {
    const currentTime = new Date();

    this.id = id;
    this.ownerId = ownerId;
    this.title = title;
    this.description = description;
    this.category = category;
    this.status = status;
    this.isbn = isbn;
    this.imageUrl = imageUrl;
    this.author = "";
    this.modifiedAt = modifiedAt ?? currentTime;
    this.createdAt = createdAt ?? currentTime;
  }

  public getId(): BookId {
    return this.id;
  }

  public getTitle(): string {
    return this.title;
  }

  public getDescription(): string {
    return this.description;
  }

  public getCategory(): string {
    return this.category;
  }

  public getStatus(): string {
    return this.status;
  }

  public getIsbn(): string {
    return this.isbn;
  }

  public getOwnerId(): UserId {
    return this.ownerId;
  }

  public getImageUrl(): string {
    return this.imageUrl;
  }

  public getAuthor(): string {
    return this.author;
  }

  public getModifiedAt(): Date {
    return this.modifiedAt;
  }

  public getCreatedAt(): Date {
    return this.createdAt;
  }
}

export default Book;
