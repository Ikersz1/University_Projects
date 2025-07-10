'use client';

import { useEffect, useState } from "react";
import { useRouter } from "next/navigation";
import { getDocs, collection } from "firebase/firestore";
import { auth, db } from "../../firebaseConfig";

import FirebaseBookRepository from "@/bookmatch/book/infrastructure/persistence/firebase-book.repository";
import BookPublisher from "@/bookmatch/book/application/book-publisher.use-case";
import UserId from "@/bookmatch/user/domain/user-id.value-object";

export default function CrearLibroForm() {
  // Book fields
  const [titulo, setTitulo] = useState("");
  const [descripcion, setDescripcion] = useState("");
  const [categoria, setCategoria] = useState("");
  const [isbn, setIsbn] = useState("");
  const [estado, setEstado] = useState("");
  const [image, setImagen] = useState<File | null>(null);
  const [categorias, setCategorias] = useState<{ id: string; nombre: string }[]>([]);

  // Promise status
  const [error, setError] = useState("");
  const [mensaje, setMensaje] = useState("");
  const [loading, setLoading] = useState(false);

  const router = useRouter();
  const repository = new FirebaseBookRepository(db);
  const publisher = new BookPublisher(repository);

  useEffect(() => {
    const fetchCategorias = async () => {
      try {
        const querySnapshot = await getDocs(collection(db, "categories"));
        const data = querySnapshot.docs.map(doc => ({
          id: doc.id,
          nombre: doc.data().nombre || "Sin nombre",
        }));
        setCategorias(data);
      } catch (error) {
        console.error("Error al obtener categorías:", error);
      }
    };

    fetchCategorias();
  }, []);

  const handleSubmit = async (e: React.FormEvent) => {
    e.preventDefault();

    if (!titulo || !descripcion || !categoria || !isbn || !estado || !image) {
      setError("Todos los campos son obligatorios (incluye una imagen)");
      return;
    }

    const currentUser = auth.currentUser;
    if (!currentUser?.uid) {
      setError("Debes iniciar sesión para añadir un libro");
      return;
    }

    try {
      setLoading(true);
      setError("");

      await publisher.publish(
        new UserId(currentUser.uid),
        titulo,
        descripcion,
        categoria,
        estado,
        isbn,
        image
      );

      setMensaje("Libro creado correctamente 🎉");
      setTitulo(""); setDescripcion(""); setCategoria(""); setIsbn(""); setEstado(""); setImagen(null);
      router.push("/mis-libros");
    } catch (err: unknown) {
      console.error(err);
      setError("Error al guardar el libro. Intenta de nuevo.");
    } finally {
      setLoading(false);
    }
  };

  return (
    <div className="max-w-[30rem] mx-auto p-6 rounded-lg shadow-2xl bg-gradient-to-b from-zinc-100 to-zinc-50/70">
      <h2 className="text-2xl font-bold mb-6 text-center">Añadir nuevo libro</h2>

      {error && <div className="mb-4 p-3 bg-red-100 text-red-700 rounded-md">{error}</div>}
      {mensaje && <div className="mb-4 p-3 bg-green-100 text-green-800 rounded-md">{mensaje}</div>}

      <form onSubmit={handleSubmit} className="space-y-4">
        <div>
          <label htmlFor="titulo" className="block text-sm font-medium text-zinc-800 mb-1">Título</label>
          <input type="text" id="titulo" className="form-input w-full" value={titulo} onChange={(e) => setTitulo(e.target.value)} required />
        </div>

        <div>
          <label htmlFor="descripcion" className="block text-sm font-medium text-zinc-800 mb-1">Descripción</label>
          <textarea id="descripcion" className="form-input w-full" value={descripcion} onChange={(e) => setDescripcion(e.target.value)} required />
        </div>

        <div>
          <label htmlFor="categoria" className="block text-sm font-medium text-zinc-800 mb-1">Categoría</label>
          <select id="categoria" value={categoria} onChange={(e) => setCategoria(e.target.value)} className="form-input w-full" required>
            <option value="">Selecciona una categoría</option>
            {categorias.map((cat) => (
              <option key={cat.id} value={cat.id}>{cat.nombre}</option>
            ))}
          </select>
        </div>

        <div>
          <label htmlFor="isbn" className="block text-sm font-medium text-zinc-800 mb-1">ISBN</label>
          <input type="text" id="isbn" className="form-input w-full" value={isbn} onChange={(e) => setIsbn(e.target.value)} required />
        </div>

        <div>
          <label htmlFor="estado" className="block text-sm font-medium text-zinc-800 mb-1">Estado del libro</label>
          <input type="text" id="estado" className="form-input w-full" value={estado} onChange={(e) => setEstado(e.target.value)} required />
        </div>

        <div>
          <label htmlFor="imagen" className="block text-sm font-medium text-zinc-800 mb-1">Imagen del libro</label>
          <input
            type="file"
            id="imagen"
            accept="image/*"
            onChange={(e) => setImagen(e.target.files?.[0] || null)}
            className="form-input w-full"
            required
          />
        </div>

        <div>
          <button type="submit" disabled={loading} className="btn text-white bg-zinc-900 hover:bg-zinc-800 w-full shadow">
            {loading ? "Creando libro..." : "Añadir libro"}
          </button>
        </div>
      </form>
    </div>
  );
}
