'use client'

import { useEffect, useState } from "react";
import { useRouter } from "next/navigation";
import { getDocs, collection } from "firebase/firestore";
import { auth, db } from "../../firebaseConfig";

import FirebaseBookRepository from "@/bookmatch/book/infrastructure/persistence/firebase-book.repository";
import BookPublisher from "@/bookmatch/book/application/book-publisher.use-case";
import UserId from "@/bookmatch/user/domain/user-id.value-object";
import Book from "@/bookmatch/book/domain/book.entity";

interface EditarLibroFormProps {
  libro: Book;
}

export default function EditarLibroForm({ libro }: EditarLibroFormProps) {
  const [titulo, setTitulo] = useState("");
  const [descripcion, setDescripcion] = useState("");
  const [categoria, setCategoria] = useState("");
  const [isbn, setIsbn] = useState("");
  const [estado, setEstado] = useState("");
  const [image, setImagen] = useState<File | null>(null);
  const [categorias, setCategorias] = useState<{ id: string; nombre: string }[]>([]);
  const [error, setError] = useState("");
  const [mensaje, setMensaje] = useState("");
  const [loading, setLoading] = useState(true);

  const router = useRouter();
  const repository = new FirebaseBookRepository(db);
  const publisher = new BookPublisher(repository);

  useEffect(() => {
    const user = auth.currentUser;
    if (!user) {
      router.push("/login");
      return;
    }

    if (!libro || libro.getOwnerId().getValue() !== user.uid) {
      router.push("/unauthorized");
      return;
    }

    setTitulo(libro.getTitle());
    setDescripcion(libro.getDescription());
    setCategoria(libro.getCategory());
    setEstado(libro.getStatus());
    setIsbn(libro.getIsbn());

    getDocs(collection(db, "categories"))
      .then((querySnapshot) => {
        const data = querySnapshot.docs.map(doc => ({
          id: doc.id,
          nombre: doc.data().nombre || "Sin nombre",
        }));
        setCategorias(data);
      })
      .catch(() => setError("Error cargando categorías."))
      .finally(() => setLoading(false));
  }, [libro, router]);

  const handleSubmit = async (e: React.FormEvent) => {
    e.preventDefault();

    if (!titulo || !descripcion || !categoria || !isbn || !estado) {
      setError("Todos los campos son obligatorios.");
      return;
    }

    const user = auth.currentUser;
    if (!user) {
      setError("Debes iniciar sesión.");
      return;
    }

    try {
      setError("");
      setLoading(true);

      await publisher.update(
        libro.getId().getValue(),
        new UserId(user.uid),
        titulo,
        descripcion,
        categoria,
        estado,
        isbn,
        image
      );

      setMensaje("Libro actualizado correctamente.");
      router.push("/mis-libros");
    } catch (err) {
      console.error(err);
      setError("Error al actualizar el libro.");
    } finally {
      setLoading(false);
    }
  };

  if (loading) return <p className="text-center mt-10">Cargando libro...</p>;

  return (
    <div className="max-w-[30rem] mx-auto p-6 rounded-lg shadow-2xl bg-gradient-to-b from-zinc-100 to-zinc-50/70">
      <h2 className="text-2xl font-bold mb-6 text-center">Editar libro</h2>

      {error && <div className="mb-4 p-3 bg-red-100 text-red-700 rounded-md">{error}</div>}
      {mensaje && <div className="mb-4 p-3 bg-green-100 text-green-800 rounded-md">{mensaje}</div>}

      <form onSubmit={handleSubmit} className="space-y-4">
        <div>
          <label htmlFor="titulo" className="block text-sm font-medium">Título</label>
          <input type="text" id="titulo" className="form-input w-full" value={titulo} onChange={(e) => setTitulo(e.target.value)} required />
        </div>
        <div>
          <label htmlFor="descripcion" className="block text-sm font-medium">Descripción</label>
          <textarea id="descripcion" className="form-input w-full" value={descripcion} onChange={(e) => setDescripcion(e.target.value)} required />
        </div>
        <div>
          <label htmlFor="categoria" className="block text-sm font-medium">Categoría</label>
          <select id="categoria" value={categoria} onChange={(e) => setCategoria(e.target.value)} className="form-input w-full" required>
            <option value="">Selecciona una categoría</option>
            {categorias.map((cat) => (
              <option key={cat.id} value={cat.id}>{cat.nombre}</option>
            ))}
          </select>
        </div>
        <div>
          <label htmlFor="isbn" className="block text-sm font-medium">ISBN</label>
          <input type="text" id="isbn" className="form-input w-full" value={isbn} onChange={(e) => setIsbn(e.target.value)} required />
        </div>
        <div>
          <label htmlFor="estado" className="block text-sm font-medium">Estado</label>
          <input type="text" id="estado" className="form-input w-full" value={estado} onChange={(e) => setEstado(e.target.value)} required />
        </div>
        <div>
          <label htmlFor="imagen" className="block text-sm font-medium">Nueva imagen (opcional)</label>
          <input type="file" id="imagen" accept="image/*" onChange={(e) => setImagen(e.target.files?.[0] || null)} className="form-input w-full" />
        </div>
        <div>
          <button type="submit" disabled={loading} className="btn text-white bg-zinc-900 hover:bg-zinc-800 w-full shadow">
            {loading ? "Actualizando..." : "Guardar cambios"}
          </button>
        </div>
      </form>
    </div>
  );
}
