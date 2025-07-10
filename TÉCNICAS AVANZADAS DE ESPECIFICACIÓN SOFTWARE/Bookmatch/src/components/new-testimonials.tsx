'use client'
import Testimonial from '@/components/new-testimonial'
import TestimonialImg01 from '@/public/images/testimonial-01.jpg'
import TestimonialImg02 from '@/public/images/testimonial-02.jpg'
import TestimonialImg03 from '@/public/images/testimonial-03.jpg'
import TestimonialImg04 from '@/public/images/testimonial-04.jpg'
import TestimonialImg05 from '@/public/images/testimonial-05.jpg'
import TestimonialImg06 from '@/public/images/testimonial-06.jpg'
import TestimonialImg07 from '@/public/images/testimonial-07.jpg'
import TestimonialImg08 from '@/public/images/testimonial-08.jpg'

export default function NewTestimonials() {

  const testimonials01 = [
    {
      image: TestimonialImg01,
      name: 'Elena García',
      user: '@elenag',
      link: '#0',
      content: 'Gracias a esta app he conseguido libros que llevaba tiempo buscando, y además he podido darle una segunda vida a los que ya no usaba. ¡Una idea genial!',
    },
    {
      image: TestimonialImg02,
      name: 'Sofía Martínez',
      user: '@sofiam',
      link: '#0',
      content: 'Muy fácil de usar y con una comunidad muy activa. En pocos días hice mi primer intercambio y fue todo un éxito.',
    },
    {
      image: TestimonialImg03,
      name: 'Ana López',
      user: '@analopez',
      link: '#0',
      content: 'Me encanta poder intercambiar libros sin tener que vender ni comprar. Es sostenible, económico y conoces gente con gustos parecidos.',
    },
    {
      image: TestimonialImg04,
      name: 'María Fernández',
      user: '@mariafdez',
      link: '#0',
      content: 'Tenía estanterías llenas de libros que ya no leía. Ahora esos libros tienen nuevos lectores, y yo he descubierto joyas que no conocía.',
    },
  ]

  const testimonials02 = [
    {
      image: TestimonialImg05,
      name: 'Carmen Ruiz',
      user: '@carmenr',
      link: '#0',
      content: 'Perfecta para estudiantes o amantes de la lectura que no quieren gastar mucho. La app funciona muy bien y los envíos son fáciles de coordinar.',
    },
    {
      image: TestimonialImg06,
      name: 'Luis Hernández',
      user: '@luis_h',
      link: '#0',
      content: 'Al principio tenía mis dudas, pero todo fue muy fluido. Ahora reviso la app cada semana para ver qué novedades hay.',
    },
    {
      image: TestimonialImg07,
      name: 'Carlos Moreno',
      user: '@carlosm',
      link: '#0',
      content: 'Intercambiar libros es mucho mejor que acumularlos. Esta plataforma lo hace muy accesible y seguro. ¡Estoy encantado!',
    },
    {
      image: TestimonialImg08,
      name: 'Alejandra Torres',
      user: '@ale_torres',
      link: '#0',
      content: 'Una iniciativa maravillosa. He descubierto autores nuevos y me he deshecho de libros que solo ocupaban espacio.',
    },
  ];

  return (
    <section className="bg-white">
      <div className="py-12 md:py-20">
        <div className="max-w-[94rem] mx-auto space-y-6">
          {/* Row #1 */}
          <div className="w-full inline-flex flex-nowrap overflow-hidden [mask-image:_linear-gradient(to_right,transparent_0,_black_28%,_black_calc(100%-28%),transparent_100%)] group">
            <div className="flex items-start justify-center md:justify-start [&>div]:mx-3 animate-infinite-scroll group-hover:[animation-play-state:paused]">
              {/* Items */}
              {testimonials01.map((testimonial, index) => (
                <Testimonial key={index} testimonial={testimonial}>
                  {testimonial.content}
                </Testimonial>
              ))}
            </div>
            {/* Duplicated element for infinite scroll */}
            <div className="flex items-start justify-center md:justify-start [&>div]:mx-3 animate-infinite-scroll group-hover:[animation-play-state:paused]" aria-hidden="true">
              {/* Items */}
              {testimonials01.map((testimonial, index) => (
                <Testimonial key={index} testimonial={testimonial}>
                  {testimonial.content}
                </Testimonial>
              ))}
            </div>
          </div>
          {/* Row #2 */}
          <div className="w-full inline-flex flex-nowrap overflow-hidden [mask-image:_linear-gradient(to_right,transparent_0,_black_28%,_black_calc(100%-28%),transparent_100%)] group">
            <div className="flex items-start justify-center md:justify-start [&>div]:mx-3 animate-infinite-scroll-inverse group-hover:[animation-play-state:paused] [animation-delay:-7.5s]">
              {/* Items */}
              {testimonials02.map((testimonial, index) => (
                <Testimonial key={index} testimonial={testimonial}>
                  {testimonial.content}
                </Testimonial>
              ))}
            </div>
            {/* Duplicated element for infinite scroll */}
            <div className="flex items-start justify-center md:justify-start [&>div]:mx-3 animate-infinite-scroll-inverse group-hover:[animation-play-state:paused] [animation-delay:-7.5s]" aria-hidden="true">
              {/* Items */}
              {testimonials02.map((testimonial, index) => (
                <Testimonial key={index} testimonial={testimonial}>
                  {testimonial.content}
                </Testimonial>
              ))}
            </div>
          </div >
        </div >
      </div >
    </section >
  )
}