import {
  doc,
  getDoc,
  getDocs,
  setDoc,
  deleteDoc,
  updateDoc,
  collection,
  Firestore,
  limit as fbLimit,
  orderBy,
  query
} from "firebase/firestore";
import Book from "@/bookmatch/book/domain/book.entity";
import BookId from "@/bookmatch/book/domain/book-id.value-object";
import UserId from "@/bookmatch/user/domain/user-id.value-object";
import BookRepository from "@/bookmatch/book/domain/book.repository";

interface FirestoreBookData {
  titulo?: string;
  título?: string;
  descripcion?: string;
  descripción?: string;
  categoria?: string;
  categoría?: string;
  estado?: string;
  status?: string;
  isbn?: string;
  propietario?: string;
  owner?: string;
  imageUrl?: string;
  creadoEn?: string | Date;
  modified_at?: string | Date;
  mofified_at?: string | Date;
}

class FirebaseBookRepository implements BookRepository {
  private static readonly COLLECTION = "books";

  private readonly db: Firestore;

  public constructor(db: Firestore) {
    this.db = db;
  }

  public async findById(id: BookId): Promise<Book | null> {
    const bookRef = doc(
      this.db,
      FirebaseBookRepository.COLLECTION,
      id.getValue(),
    );
    const snapshot = await getDoc(bookRef);

    if (!snapshot.exists()) return null;

    return this.fromFirestoreToDomain(snapshot.data(), snapshot.id);
  }

  public async findByUserId(userId: UserId): Promise<Book[]> {
    const snapshot = await getDocs(
      collection(this.db, FirebaseBookRepository.COLLECTION),
    );
    const books = snapshot.docs
      .filter((doc) => {
        const data = doc.data();
        return (data.propietario || data.owner) === userId.getValue();
      })
      .map((doc) => this.fromFirestoreToDomain(doc.data(), doc.id));

    return books;
  }

  public async searchAll(): Promise<Book[]> {
    const snapshot = await getDocs(
      collection(this.db, FirebaseBookRepository.COLLECTION),
    );

    const books = snapshot.docs.map((doc) =>
      this.fromFirestoreToDomain(doc.data(), doc.id),
    );

    return books;
  }

  public async searchAllPopular(limit: number): Promise<Book[]> {
    const booksRef = collection(this.db, FirebaseBookRepository.COLLECTION);

    const booksQuery = query(booksRef, orderBy("creadoEn", "desc"), fbLimit(limit));

    const snapshot = await getDocs(booksQuery);

    return snapshot.docs.map((doc) =>
      this.fromFirestoreToDomain(doc.data(), doc.id)
    );
  }

  public async save(book: Book): Promise<void> {
    const bookRef = doc(
      this.db,
      FirebaseBookRepository.COLLECTION,
      book.getId().getValue(),
    );
    await setDoc(bookRef, this.fromDomainToFirestore(book));
  }

  async update(book: Book): Promise<void> {
    const docRef = doc(this.db, "books", book.getId().getValue());

    const dataToUpdate: Partial<FirestoreBookData> = {
      titulo: book.getTitle(),
      descripcion: book.getDescription(),
      categoria: book.getCategory(),
      estado: book.getStatus(),
      isbn: book.getIsbn(),
    };

    if (book.getImageUrl()) {
      dataToUpdate.imageUrl = book.getImageUrl();
    }

    await updateDoc(docRef, dataToUpdate);
  }

  public async delete(book: Book): Promise<void> {
    const bookRef = doc(
      this.db,
      FirebaseBookRepository.COLLECTION,
      book.getId().getValue(),
    );
    await deleteDoc(bookRef);
  }

  private fromFirestoreToDomain(data: FirestoreBookData, id: string): Book {
    const currentTime = new Date();

    const createdAt = this.toDateOrNow(data.creadoEn, currentTime);
    const modifiedAt = this.toDateOrNow(
      data.modified_at || data.mofified_at,
      currentTime,
    );

    return new Book(
      new BookId(id),
      new UserId(data.propietario || data.owner || ""),
      data.titulo || data.título || "",
      data.descripcion || data.descripción || "",
      data.categoria || data.categoría || "",
      data.estado || data.status || "",
      data.isbn || "",
      data.imageUrl || "/images/dummy-book-01.jpg",
      modifiedAt,
      createdAt,
    );
  }

  private fromDomainToFirestore(book: Book): FirestoreBookData {
    return {
      titulo: book.getTitle(),
      descripcion: book.getDescription(),
      categoria: book.getCategory(),
      estado: book.getStatus(),
      isbn: book.getIsbn(),
      propietario: book.getOwnerId().getValue(),
      modified_at: book.getModifiedAt(),
      creadoEn: book.getCreatedAt(),
      imageUrl: book.getImageUrl(),
    };
  }

  private toDateOrNow(
    input: Date | { toDate: () => Date } | string | null | undefined,
    fallback: Date,
  ): Date {
    if (input instanceof Date) return input;

    if (typeof input === 'string') {
      const parsed = new Date(input);
      return isNaN(parsed.getTime()) ? fallback : parsed;
    }

    if (input?.toDate instanceof Function) return input.toDate();

    return fallback;
  }
}

export default FirebaseBookRepository;
