import ChatRoom from "@/components/chat/ChatRoom";
import Header from "@/components/ui/header";

interface ChatPageProps {
  params: Promise<{ chatId: string }>;
}

export default async function ChatPage({ params }: ChatPageProps) {
  const _params = await params;

  return (
    <>
      <Header />
      <main className="grow py-10 px-4 max-w-2xl mx-auto">
        <h1 className="text-2xl font-bold mb-6 text-center">Chat</h1>
        <ChatRoom chatId={_params.chatId} />
      </main>
    </>
  );
}
