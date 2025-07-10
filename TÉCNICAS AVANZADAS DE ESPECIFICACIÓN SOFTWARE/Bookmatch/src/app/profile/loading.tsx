export default function Loading() {
  return (
    <div className="max-w-3xl mx-auto text-center pb-12 md:pb-16">
      <h1 className="font-inter-tight text-4xl md:text-5xl font-bold bg-clip-text text-transparent bg-gradient-to-r from-zinc-500 via-zinc-900 to-zinc-900">
        Mi Perfil
      </h1>
      <div className="flex justify-center items-center min-h-[400px]">
        <div className="animate-spin rounded-full h-12 w-12 border-t-2 border-b-2 border-zinc-900"></div>
      </div>
    </div>
  )
}

