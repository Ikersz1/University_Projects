import Image from "next/image";
import Link from "next/link";

export type NewGalleryItemProps = {
  id: string
  image: string
}

const NewGalleryItem = (props: NewGalleryItemProps) => {
  const rotateClasses = ["rotate-1", "rotate-2", "rotate-3", "-rotate-1", "-rotate-2", "-rotate-3"];
  const randomRotateClass = rotateClasses[Math.floor(Math.random() * rotateClasses.length)];

  return (
    <div className={`origin-center ${randomRotateClass} transition delay-[100] duration-300 ease-in-out hover:-translate-y-1 hover:rotate-0 hover:scale-110`}>
      <Link href={`/books/${props.id}`}>
        <Image className="rounded-md" src={props.image} width={300} height={200} alt="..." />
      </Link>
    </div>
  );
}

export default NewGalleryItem;