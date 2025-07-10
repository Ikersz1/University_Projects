'use client';

import { Repeat } from "lucide-react";
import { useEffect, useState } from 'react';
import {
  collection,
  addDoc,
  onSnapshot,
  query,
  orderBy,
  getDocs,
  where,
  doc,
  getDoc
} from 'firebase/firestore';
import { auth, db } from "../../firebaseConfig";

interface ChatRoomProps {
  chatId: string;
}

interface Message {
  id?: string;
  senderId: string;
  text: string;
  timestamp: string;
}

interface UserBook {
  id: string;
  title: string;
}

export default function ChatRoom({ chatId }: ChatRoomProps) {
  const [messages, setMessages] = useState<Message[]>([]);
  const [newMessage, setNewMessage] = useState('');
  const [showDropdown, setShowDropdown] = useState(false);
  const [userBooks, setUserBooks] = useState<UserBook[]>([]);
  const [loadingBooks, setLoadingBooks] = useState(false);
  const [isOwner, setIsOwner] = useState(false); // ✅ Nuevo estado

  useEffect(() => {
    const q = query(
      collection(db, 'chats', chatId, 'messages'),
      orderBy('timestamp', 'asc')
    );

    const unsubscribe = onSnapshot(q, (snapshot) => {
      const msgs: Message[] = snapshot.docs.map(doc => ({
        id: doc.id,
        ...(doc.data() as Message)
      }));
      setMessages(msgs);
    });

    return () => unsubscribe();
  }, [chatId]);

  // ✅ Verificar si el usuario actual es el propietario del libro en el chat
  useEffect(() => {
    const checkOwnership = async () => {
      const user = auth.currentUser;
      if (!user) return;

      try {
        const chatRef = doc(db, "chats", chatId);
        const chatSnap = await getDoc(chatRef);
        const libroId = chatSnap.data()?.libro?.id;

        if (libroId) {
          const libroRef = doc(db, "books", libroId);
          const libroSnap = await getDoc(libroRef);
          const propietario = libroSnap.data()?.propietario;
          setIsOwner(propietario === user.uid);
        }
      } catch (err) {
        console.error("Error comprobando propiedad del libro:", err);
      }
    };

    checkOwnership();
  }, [chatId]);

  const handleSendMessage = async () => {
    const user = auth.currentUser;
    if (!user || !newMessage.trim()) return;

    await addDoc(collection(db, 'chats', chatId, 'messages'), {
      senderId: user.uid,
      text: newMessage.trim(),
      timestamp: new Date()
    });

    setNewMessage('');
  };

  const loadUserBooks = async () => {
    if (!auth.currentUser) return;
    setLoadingBooks(true);
    try {
      const q = query(collection(db, "books"), where("propietario", "==", auth.currentUser.uid));
      const snapshot = await getDocs(q);
      const books: UserBook[] = [];
      snapshot.forEach(doc => {
        books.push({
          id: doc.id,
          title: doc.data().titulo || "Sin título"
        });
      });
      setUserBooks(books);
    } catch (error) {
      console.error("Error al cargar libros:", error);
    } finally {
      setLoadingBooks(false);
    }
  };

  const sendExchangeRequest = async (bookOfrecidoId: string) => {
    const user = auth.currentUser;
    if (!user) return;

    try {
      const chatRef = doc(db, "chats", chatId);
      const chatSnap = await getDoc(chatRef);
      if (!chatSnap.exists()) throw new Error("Chat no encontrado.");

      const chatData = chatSnap.data();
      const libroSolicitadoId = chatData?.libro?.id;
      const usuarioReceptor = (chatData?.usuarios || []).find((u: string) => u !== user.uid);

      const now = new Date();

      const libroRef = doc(db, "books", bookOfrecidoId);
      const libroSnap = await getDoc(libroRef);
      const tituloOfrecido = libroSnap.exists() ? libroSnap.data().titulo || "Libro sin título" : "Libro sin título";

      await addDoc(collection(db, "requests"), {
        libroOfrecido: bookOfrecidoId,
        libroSolicitado: libroSolicitadoId,
        usuarioSolicitante: user.uid,
        usuarioReceptor,
        chatId,
        createdAt: now,
        fecha: now.toISOString().split('T')[0],
        hora: now.toTimeString().split(' ')[0],
        rechazado: false
      });

      await addDoc(collection(db, 'chats', chatId, 'messages'), {
        senderId: "sistema",
        text: `🔁 Se ha propuesto un intercambio con el libro: "${tituloOfrecido}"`,
        timestamp: now
      });

      setShowDropdown(false);
    } catch (err) {
      console.error("Error al enviar la solicitud de intercambio:", err);
    }
  };

  const handleProposeExchange = () => {
    if (!showDropdown) loadUserBooks();
    setShowDropdown(!showDropdown);
  };

  return (
    <div className="space-y-4 h-full flex flex-col">
      <div className="border rounded p-4 flex-1 overflow-y-auto bg-zinc-50">
        {messages.map(msg => {
          const isOwnMessage = msg.senderId === auth.currentUser?.uid;
          const isSystemMessage = msg.senderId === "sistema" || msg.text.startsWith("🔁");

          return (
            <div
              key={msg.id}
              className={`mb-2 flex ${
                isSystemMessage
                  ? "justify-center"
                  : isOwnMessage
                  ? "justify-end"
                  : "justify-start"
              }`}
            >
              <span
                className={`inline-block px-3 py-2 rounded-lg break-words text-sm max-w-sm
                  ${
                    isSystemMessage
                      ? "bg-green-100 text-green-800 font-medium text-center"
                      : isOwnMessage
                      ? "bg-blue-500 text-white"
                      : "bg-gray-200 text-gray-800"
                  }`}
              >
                {msg.text}
              </span>
            </div>
          );
        })}
      </div>

      <div className="flex items-center space-x-2 pt-2">
        <input
          type="text"
          value={newMessage}
          onChange={e => setNewMessage(e.target.value)}
          className="flex-grow border px-3 py-2 rounded"
          placeholder="Escribe tu mensaje..."
        />
        <button
          onClick={handleSendMessage}
          className="bg-zinc-900 text-white px-4 py-2 rounded hover:bg-zinc-800"
        >
          Enviar
        </button>

        {!isOwner && ( // ✅ Solo muestra el botón si no es propietario
          <div className="relative">
            <button
              onClick={handleProposeExchange}
              className="bg-yellow-500 text-white px-2 py-2 rounded hover:bg-yellow-400"
            >
              <Repeat className="w-5 h-5 text-white" />
            </button>

            {showDropdown && (
              <div className="absolute bottom-full mb-2 right-0 w-64 bg-white border rounded shadow-lg z-10 max-h-60 overflow-y-auto">
                {loadingBooks ? (
                  <div className="p-3 text-center text-sm text-gray-500">Cargando libros...</div>
                ) : userBooks.length > 0 ? (
                  <ul>
                    {userBooks.map(book => (
                      <li
                        key={book.id}
                        className="p-2 text-sm hover:bg-gray-100 cursor-pointer"
                        onClick={() => sendExchangeRequest(book.id)}
                      >
                        {book.title}
                      </li>
                    ))}
                  </ul>
                ) : (
                  <div className="p-3 text-sm text-gray-500 text-center">No tienes libros disponibles.</div>
                )}
              </div>
            )}
          </div>
        )}
      </div>
    </div>
  );
}
