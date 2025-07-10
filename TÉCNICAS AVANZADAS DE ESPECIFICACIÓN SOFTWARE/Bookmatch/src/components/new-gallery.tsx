import { useMemo } from "react";

import NewGalleryItem, { NewGalleryItemProps } from "@/components/new-gallery-item";

type NewGalleryProps = {
  title: string,
  items: NewGalleryItemProps[]
};

const shuffleArray = (array: NewGalleryItemProps[]) => {
  const shuffled = [...array];
  for (let i = shuffled.length - 1; i > 0; i--) {
    const j = Math.floor(Math.random() * (i + 1));
    [shuffled[i], shuffled[j]] = [shuffled[j], shuffled[i]];
  }
  return shuffled;
};

const NewGallery = (props: NewGalleryProps) => {
  const shuffledBooks = useMemo(() => shuffleArray(props.items), [props.items]);

  return (
    <div className="flex flex-col mx-12 lg:mx-auto max-w-6xl py-16 items-center">
      <p className="text-2xl lg:text-3xl font-black text-gray-800">{props.title}</p>
      <div className="grid grid-cols-2 md:grid-cols-4 lg:grid-cols-5 gap-x-4 md:gap-x-0 gap-y-8 mt-4">
        {shuffledBooks.map((book, key) => <NewGalleryItem key={key} id={book.id} image={book.image} />)}
      </div>
    </div>
  );
};

export default NewGallery;
