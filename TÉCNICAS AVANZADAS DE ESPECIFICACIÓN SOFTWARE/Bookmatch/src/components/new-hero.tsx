const NewHero = () => {
  return (
    <div className="flex flex-col items-center py-16 md:py-32 px-4">
      <div className="max-w-6xl w-full text-center space-y-2">
        <h1 className="text-[4rem] md:text-9xl font-black bg-clip-text bg-gradient-to-r from-yellow-400 via-red-500 to-purple-400 text-transparent min-w-fit">
          BookMatch
        </h1>
        <p className="text-xl font-light text-gray-900 max-w-2xl mx-auto">
          ¡Miles de libros te esperan para que hagas <b className="font-black">MATCH</b> con ellos!
        </p>
      </div>
    </div>
  )
};

export default NewHero;
