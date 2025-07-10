<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Faker\Factory as Faker;
use App\Models\Partido;
use App\Models\Equipo;
use Carbon\Carbon;

class PartidosTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        // Obtener todos los equipos
        $equipos = Equipo::all();

        // Mapeo de nombres de equipos a estadios correspondientes
        $estadiosPorEquipo = [
            'REAL MADRID' => 'Santiago Bernabéu',
            'FC BARCELONA' => 'Camp Nou',
            'GIRONA FC' => 'Municipal de Montilivi',
            'ATLÉTICO DE MADRID' => 'Civitas Metropolitano',
            'ATHLETIC CLUB' => 'San Mamés Barria',
            'REAL SOCIEDAD' => 'Reale Arena',
            'VALENCIA CF' => 'Mestalla',
            'REAL BETIS' => 'Benito Villamarín',
            'CA OSASUNA' => 'El Sadar',
            'VILLAREAL CF' => 'La Cerámica',
            'GETAFE CF' => 'Coliseum',
            'UD LAS PALMAS' => 'Gran Canaria',
            'DEPORTIVO ALAVÉS' => 'Mendizorroza',
            'SEVILLA FC' => 'Ramón Sánchez Pinzjuán',
            'RCD MALLORCA' => 'Mallorca Son Moix',
            'RAYO VALLECANO' => 'Vallecas',
            'RC CELTA' => 'Abanca-Balaídos',
            'CÁDIZ CF' => 'Nuevo Mirandilla',
            'GRANADA CF' => 'Nuevo Los Cármenes',
            'UD ALMERÍA' => 'Power Horse Stadium'
        ];

        // Generar partidos para 38 jornadas
        $numJornadas = 38;
        $partidosPorJornada = 10;

        // Fecha de inicio para los partidos (antes del 18/04/2024)
        $fechaInicio = Carbon::createFromFormat('Y-m-d', '2024-04-22');
        $horaBase = '15:00:00'; // Hora base para los partidos (puede ajustarse)

        // Mezclar los equipos de forma aleatoria
        $equiposAleatorios = $equipos->shuffle()->toArray();

        // Iterar sobre las jornadas
        // Iterar sobre las jornadas
        for ($jornada = 1; $jornada <= $numJornadas; $jornada++) {
            // Mezclar los equipos de forma aleatoria para cada jornada
            $equiposAleatorios = $equipos->shuffle()->toArray();

            // Incrementar la fecha para cada jornada (diferente día)
            $fechaJornada = $fechaInicio->copy()->addDays($jornada - 1);

            // Iterar sobre los partidos por jornada
            for ($partido = 0; $partido < $partidosPorJornada; $partido++) {
                $indiceLocal = $partido * 2;
                $indiceVisitante = $partido * 2 + 1;

                $equipoLocal = $equiposAleatorios[$indiceLocal];
                $equipoVisitante = $equiposAleatorios[$indiceVisitante];

                // Establecer la fecha y hora del partido según la distribución requerida
                if ($partido < 4) {
                    $fechaPartido = $fechaJornada->copy(); // Primeros cuatro partidos en el mismo día
                } elseif ($partido < 7) {
                    $fechaPartido = $fechaJornada->copy()->addDay(); // Siguientes tres partidos al día siguiente
                } else {
                    $fechaPartido = $fechaJornada->copy()->addDays(2); // Últimos tres partidos dos días después
                }

                // Hora base para los partidos (puede ajustarse)
                $horaPartido = Carbon::createFromFormat('H:i:s', '15:00:00')
                    ->addMinutes($partido * 30); // Ajustar la hora a XX:00 o XX:30

                // Crear el partido en la base de datos
                if($jornada > 2){
                    $resultado = ' - ';
                }
                else{
                    $resultado = rand(0, 5) . ' - ' . rand(0, 5); // Generar resultado aleatorio
                }

                Partido::create([
                    'fecha' => $fechaPartido->format('Y-m-d'),
                    'hora' => $horaPartido->format('H:i:s'),
                    'estadio' => $estadiosPorEquipo[strtoupper($equipoLocal['nombre'])], // Estadio basado en el nombre del equipo local
                    'resultado' => $resultado,
                    'jornada' => $jornada,
                    'equipo_local_id' => $equipoLocal['id'],
                    'equipo_visitante_id' => $equipoVisitante['id'],
                ]);
            }
        }
    }
}
