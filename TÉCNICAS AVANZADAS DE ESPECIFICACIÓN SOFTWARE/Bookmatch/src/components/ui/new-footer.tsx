const NewFooter = () => {
  return (
    <footer className="bg-white border-t border-gray-200 py-8 mt-8">
      <div className="max-w-6xl mx-auto px-6 grid md:grid-cols-3 gap-6 text-gray-700">
        <div>
          <h3 className="text-2xl font-black bg-clip-text bg-gradient-to-r from-yellow-400 via-red-500 to-purple-400 text-transparent">BookMatch</h3>
          <p className="mt-2 text-md font-light">
            ¡Miles de libros te esperan para que hagas <span className="font-black">MATCH</span> con ellos!
          </p>
        </div>

        <div className="text-lg space-y-2">
          <h4 className="font-semibold text-gray-900">Recursos</h4>
          <ul className="space-y-1">
            <li><a href="#" className="hover:bg-clip-text hover:bg-gradient-to-r hover:from-yellow-400 hover:via-red-500 hover:to-purple-400 hover:text-transparent transition">Comunidad</a></li>
            <li><a href="#" className="hover:bg-clip-text hover:bg-gradient-to-r hover:from-yellow-400 hover:via-red-500 hover:to-purple-400 hover:text-transparent transition">Colaboraciones</a></li>
            <li><a href="#" className="hover:bg-clip-text hover:bg-gradient-to-r hover:from-yellow-400 hover:via-red-500 hover:to-purple-400 hover:text-transparent transition">Términos de servicio</a></li>
          </ul>
        </div>

        <div className="text-lg space-y-2">
          <h4 className="font-semibold text-gray-900">Síguenos</h4>
          <div className="flex space-x-4">
            <a href="#" className="hover:bg-clip-text hover:bg-gradient-to-r hover:from-yellow-400 hover:via-red-500 hover:to-purple-400 hover:text-transparent transition">📘 Facebook</a>
            <a href="#" className="hover:bg-clip-text hover:bg-gradient-to-r hover:from-yellow-400 hover:via-red-500 hover:to-purple-400 hover:text-transparent transition">𝕏 X</a>
            <a href="#" className="hover:bg-clip-text hover:bg-gradient-to-r hover:from-yellow-400 hover:via-red-500 hover:to-purple-400 hover:text-transparent transition">📸 Instagram</a>
          </div>
        </div>
      </div>

      <div className="mt-8 text-center text-xs text-gray-500">
        © {new Date().getFullYear()} BookMatch. Todos los derechos reservados.
      </div>
    </footer>
  );
};

export default NewFooter;
