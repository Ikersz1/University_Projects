import Counter from '@/components/counter'

interface StatProps {
  number: number
  preffix: string
  suffix: string
  text: string
}

export default function NewStats() {

  const stats: StatProps[] = [
    {
      number: 1236,
      preffix: '+',
      suffix: ' 📚',
      text: 'Libros en movimiento',
    },
    {
      number: 335,
      preffix: '+',
      suffix: ' 🔔',
      text: '¡MATCHES!',
    },
    {
      number: 211,
      preffix: '+',
      suffix: '🤝',
      text: 'Intercambios',
    }
  ]

  return (
    <div className="md:max-w-6xl mx-auto">
      <div className="max-w-sm mx-auto grid grid-cols-3 items-stretch md:max-w-none">

        {stats.map((stat: StatProps, index: number) => (
          <div key={index} className="relative text-center md:px-5">
            <h4 className="font-inter-tight text-2xl md:text-4xl font-bold tabular-nums mb-2">
              {stat.preffix}
              <Counter number={stat.number} />
              {stat.suffix}
            </h4>
            <p className="font-light text-md text-gray-800">{stat.text}</p>
          </div>
        ))}

      </div>
    </div>
  )
}