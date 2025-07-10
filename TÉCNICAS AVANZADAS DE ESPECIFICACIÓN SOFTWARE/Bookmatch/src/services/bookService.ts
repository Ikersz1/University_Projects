import { db } from "../firebaseConfig";
import { doc, getDoc, collection, getDocs, deleteDoc } from "firebase/firestore";

export interface Book {
  id: string;
  titulo: string;
  descripcion: string;
  categoria: string;
  estado: string;
  isbn: string;
  propietario: string;
  portada?: string;  // Añadido campo portada
  autor?: string;    // Añadido campo autor
}

export async function getBookById(bookId: string): Promise<Book | null> {
  try {
    const bookRef = doc(db, "books", bookId);
    const bookSnap = await getDoc(bookRef);

    if (bookSnap.exists()) {
      const data = bookSnap.data();
      console.log("Datos originales de Firestore:", data); // Añadir log para depuración
      
      // Manejar todos los posibles nombres de campos (con y sin acentos)
      return {
        id: bookSnap.id,
        titulo: data.titulo || data.título || "",
        descripcion: data.descripcion || data.descripción || "",
        categoria: data.categoria || data.categoría || "",
        estado: data.estado || data.status || "",
        isbn: data.isbn || "",
        propietario: data.propietario || data.owner || "",
        portada: data.portada || data.cover || "",
        autor: data.autor || data.author || ""
      } as Book;
    } else {
      console.log("No existe el libro con ese ID");
      return null;
    }
  } catch (error) {
    console.error("Error al obtener el libro:", error);
    return null;
  }
}

export async function getAllBooks(): Promise<Book[]> {
  try {
    const booksCollection = collection(db, "books");
    const booksSnapshot = await getDocs(booksCollection);

    // Log raw data to see field names
    console.log("Raw Firestore data:", booksSnapshot.docs.map(doc => ({ id: doc.id, ...doc.data() })));

    const booksList = booksSnapshot.docs.map(doc => {
      const data = doc.data();
      // Check all possible field name variations
      return {
        id: doc.id,
        titulo: data.titulo || data.título || "",
        descripcion: data.descripcion || data.descripción || "",
        categoria: data.categoria || data.categoría || "",
        estado: data.estado || data.status || "",
        isbn: data.isbn || "",
        propietario: data.propietario || data.owner || "",
        portada: data.portada || data.cover || "",
        autor: data.autor || data.author || ""
      };
    });

    console.log("Processed books data:", booksList);
    return booksList;
  } catch (error) {
    console.error("Error al obtener los libros:", error);
    return [];
  }
}

export async function deleteBook(bookId: string): Promise<boolean> {
  try {
    const bookRef = doc(db, "books", bookId);
    await deleteDoc(bookRef);
    return true;
  } catch (error) {
    console.error("Error al eliminar el libro:", error);
    return false;
  }
}