<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Prediccion;
use App\Models\User;
use App\Models\Partido;

class PrediccionesTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        // Obtener todos los usuarios y partidos
        $users = User::all();
        $partidos = Partido::where('resultado', ' - ')->get(); // Obtener solo los partidos que no se han jugado

        // Iterar sobre cada partido y asignar predicciones a cada usuario
        foreach ($partidos as $partido) {
            if($partido->jornada >= 3 && $partido->jornada <= 7){
                foreach ($users as $user) {
                    // Generar un número aleatorio entre 0 y 2 para determinar qué voto será 1
                    $randomIndex = rand(0, 2);
    
                    // Asignar valores según el índice aleatorio
                    $votoLocal = ($randomIndex == 0) ? 1 : 0;
                    $votoEmpate = ($randomIndex == 1) ? 1 : 0;
                    $votoVisitante = ($randomIndex == 2) ? 1 : 0;
    
                    // Crear la predicción para el usuario y partido actual
                    Prediccion::create([
                        'user_id' => $user->id,
                        'partido_id' => $partido->id,
                        'voto_local' => $votoLocal,
                        'voto_empate' => $votoEmpate,
                        'voto_visitante' => $votoVisitante,
                    ]);
                }
            }
        }
    }
}
