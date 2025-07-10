import Link from "next/link"

export default function AboutSection() {
  return (
    <section className="bg-white py-16">
      <div className="max-w-4xl mx-auto px-4 sm:px-6">
        <div className="text-center mb-12">
          <h2 className="text-3xl font-bold mb-6">¿Qué es BookMatch?</h2>
          <p className="text-zinc-600 mb-6 text-lg leading-relaxed">
            BookMatch es una aplicación web que permite el trueque de libros de segunda mano entre los usuarios
            interesados. También, una vía rápida de buscar libros educativos para estudiantes que se matriculan en un
            nuevo curso, a la vez que te deshaces de libros de cursos anteriores. Promoviendo la reutilización y el
            acceso a materiales de estudio o lectura sin necesidad de un gran gasto económico.
          </p>

          <h3 className="text-2xl font-bold mt-12 mb-4">Grupo K S.L.</h3>
          <p className="text-zinc-600 mb-10 text-lg leading-relaxed">
            Somos una startup especializada en el diseño y desarrollo de páginas web modernas, optimizadas y
            personalizadas para pequeñas y medianas empresas. Combinamos creatividad, tecnología y enfoque en el usuario
            para ayudar a nuestros clientes a destacar en el entorno digital.
          </p>

          <Link
            href="/register"
            className="inline-block btn-lg text-white font-semibold shadow bg-gradient-to-r from-yellow-400 via-red-500 to-purple-400 hover:from-yellow-300 hover:via-red-400 hover:to-purple-300 transition rounded-md"
          >
            Regístrate ahora
          </Link>
        </div>
      </div>
    </section>
  )
}
