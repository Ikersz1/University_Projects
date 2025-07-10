import RegisterForm from "@/components/auth/RegisterForm";
import Header from "@/components/ui/header";

export const metadata = {
  title: 'Registro | BookMatch',
  description: 'Crea una cuenta en BookMatch',
}

export default function RegisterPage() {
  return (
    <>
      <Header />
      <main className="grow">
        <section className="relative">
          <div className="max-w-6xl mx-auto px-4 sm:px-6">
            <div className="pt-32 pb-12 md:pt-40 md:pb-20">
              <div className="max-w-3xl mx-auto text-center pb-12 md:pb-16">
                <h1 className="font-inter-tight text-4xl md:text-5xl font-bold bg-clip-text text-transparent bg-gradient-to-r from-zinc-500 via-zinc-900 to-zinc-900">Crear una cuenta</h1>
              </div>
              <RegisterForm />
            </div>
          </div>
        </section>
      </main>
    </>
  );
}