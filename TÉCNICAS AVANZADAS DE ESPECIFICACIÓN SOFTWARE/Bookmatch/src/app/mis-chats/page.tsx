"use client";

import Header from "@/components/ui/header";
import Footer from "@/components/ui/footer";
import { useEffect, useState } from "react";
import { auth, db } from "../../firebaseConfig";
import {
  collection,
  query,
  where,
  onSnapshot,
  doc,
  getDoc
} from "firebase/firestore";
import ChatRoom from "@/components/chat/ChatRoom";
import { UserCircle } from "lucide-react";
import Image from "next/image";

interface ChatPreview {
  id: string;
  usuarios: string[];
  libro?: {
    id: string;
    titulo: string;
    imageUrl?: string | null;
  };
}

export default function MisChatsPage() {
  const [chats, setChats] = useState<ChatPreview[]>([]);
  const [chatId, setChatId] = useState<string | null>(null);
  const [userNames, setUserNames] = useState<Record<string, string>>({});

  useEffect(() => {
    const currentUser = auth.currentUser;
    if (!currentUser) return;

    const q = query(
      collection(db, "chats"),
      where("usuarios", "array-contains", currentUser.uid)
    );

    const unsubscribe = onSnapshot(q, async (snapshot) => {
      const results: ChatPreview[] = [];
      const names: Record<string, string> = {};

      for (const docSnap of snapshot.docs) {
        const data = docSnap.data();
        const chatData: ChatPreview = {
          id: docSnap.id,
          usuarios: data.usuarios,
          libro: data.libro || undefined
        };
        results.push(chatData);

        const otherUserId = data.usuarios.find((u: string) => u !== currentUser.uid);
        if (otherUserId && !userNames[otherUserId]) {
          const userRef = doc(db, "users", otherUserId);
          const userSnap = await getDoc(userRef);
          names[otherUserId] = userSnap.exists()
            ? userSnap.data().name || "Desconocido"
            : "Desconocido";
        }
      }

      setChats(results);
      setUserNames(prev => ({ ...prev, ...names }));
    });

    return () => unsubscribe();
  }, [userNames]);

  return (
    <>
    <Header />
    <div className="mt-24 w-[1000px] mx-auto">
      <h1 className="text-2xl font-bold mb-4 flex items-center gap-2">
        <span className="text-lg"></span> Mis chats
      </h1>

      <div className="flex h-[75vh] border rounded-lg overflow-hidden shadow-lg">
        {/* Lista de chats */}
        <aside className="w-1/4 bg-zinc-50 border-r overflow-y-auto">
          {chats.map((chat) => {
            const otherUserId = chat.usuarios.find(
              (u) => u !== auth.currentUser?.uid
            ) || "Desconocido";

            const userName = userNames[otherUserId] || "Desconocido";

            return (
              <div
                key={chat.id}
                onClick={() => setChatId(chat.id)}
                className={`px-4 py-3 border-b cursor-pointer hover:bg-zinc-100 transition-all text-sm ${
                  chatId === chat.id ? "bg-zinc-200 font-medium" : ""
                }`}
              >
                <div className="flex items-center gap-2 mb-1">
                  <UserCircle className="w-5 h-5 text-zinc-500" />
                  <span className="font-medium">{userName}</span>
                </div>

                {chat.libro && (
                  <div className="flex items-center gap-2 ml-6">
                    {chat.libro.imageUrl ? (
                      <Image
                        src={chat.libro.imageUrl}
                        alt="Portada"
                        width={32}
                        height={48}
                        className="rounded shadow"
                      />
                    ) : (
                      <div className="w-8 h-12 bg-zinc-200 flex items-center justify-center text-xs rounded">
                        📚
                      </div>
                    )}
                    <span className="text-xs text-zinc-500 truncate max-w-[120px]">
                      {chat.libro.titulo}
                    </span>
                  </div>
                )}
              </div>
            );
          })}
        </aside>

        {/* Área de conversación */}
        <section className="flex-1 p-4 bg-white">
          {chatId ? (
            <ChatRoom chatId={chatId} />
          ) : (
            <div className="h-full flex items-center justify-center text-zinc-400">
              Selecciona un chat para comenzar a hablar.
            </div>
          )}
        </section>
      </div>
    </div>
    <Footer />
    </>
  );
}
