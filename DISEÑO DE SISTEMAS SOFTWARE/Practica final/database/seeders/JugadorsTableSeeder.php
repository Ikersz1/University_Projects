<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Jugador;
use App\Models\Est_jugador;
use Illuminate\Support\Facades\Storage;

class JugadorsTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $jugadores = [
            [
                'nombre' => 'Cristiano Ronaldo',
                'posicion' => 'Delantero',
                'nacionalidad' => 'Portugal',
                'edad' => 35,
                'equipo_id' => 1,
                'foto' => 'images/jugadores/cr7.webp',
                'biografia' => 'Cristiano Ronaldo, conocido simplemente como CR7, es uno de los futbolistas más famosos y condecorados del mundo. Ha ganado múltiples títulos y premios individuales en su carrera.',
                'estadisticas' => [
                    'goles' => 66,
                    'asistencias' => 54,
                    'amarillas' => 5,
                    'rojas' => 6,
                ],
            ],
            [
                'nombre' => 'Lionel Messi',
                'posicion' => 'Delantero',
                'nacionalidad' => 'Argentina',
                'edad' => 33,
                'equipo_id' => 2,
                'foto' => 'storage/images/messi.png',
                'biografia' => 'Lionel Messi es ampliamente considerado como uno de los mejores jugadores de todos los tiempos, conocido por su habilidad, visión de juego y goles espectaculares. Ha pasado la mayor parte de su carrera en el FC Barcelona.',
                'estadisticas' => [
                    'goles' => 70,
                    'asistencias' => 60,
                    'amarillas' => 4,
                    'rojas' => 2,
                ],
            ],
        ];

        foreach ($jugadores as $jugadorData) {
            $estadisticas = $jugadorData['estadisticas'];
            unset($jugadorData['estadisticas']);
            $jugador = Jugador::create($jugadorData);
            $estadisticas['jugador_id'] = $jugador->id;
            Est_jugador::create($estadisticas);
        }

        // Crear jugadores aleatorios usando el factory y asignar estadísticas por defecto
        Jugador::factory(30)->create()->each(function ($jugador) {
            Est_jugador::create([
                'jugador_id' => $jugador->id,
                'goles' => 0,
                'asistencias' => 0,
                'amarillas' => 0,
                'rojas' => 0,
            ]);
        });
    }
}
