import Image, { StaticImageData } from 'next/image'

interface TestimonialProps {
  testimonial: {
    image: StaticImageData
    name: string
    user: string
    link: string
    content: string
  }
  children: React.ReactNode
}

export default function NewTestimonial({ testimonial, children }: TestimonialProps) {
  return (
    <div className="rounded w-[22rem] p-2 m-2 bg-gradient-to-r from-yellow-400 via-red-500 to-purple-400 ">
      <div className='rounded bg-white p-2'>
        <div className="flex items-center mb-4">
          <Image className="shrink-0 rounded-full mr-3" src={testimonial.image} width={44} height={44} alt={testimonial.name} />
          <div>
            <div className="font-inter-tight font-bold text-zinc-200">{testimonial.name}</div>
            <div>
              <a className="text-sm font-medium text-zinc-500 hover:text-zinc-300 transition" href={testimonial.link}>{testimonial.user}</a>
            </div>
          </div>
        </div>
        <div className="text-zinc-500 before:content-['\0022'] after:content-['\0022']">
          {children}
        </div>
      </div>
    </div>
  )
}