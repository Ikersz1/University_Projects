<?php

namespace Database\Seeders;

use App\Models\Est_partido;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Faker\Factory as Faker;
use App\Models\Partido;
class EstPartidosTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $partidos = Partido::all();

        foreach ($partidos as $partido) {
            if($partido->resultado == ' - '){
                $golesLocal = 0;
                $golesVisitante = 0;
            } 
            else {
                // Obtener los goles del resultado del partido
                $resultado = explode('-', $partido->resultado);
                $golesLocal = trim($resultado[0]);
                $golesVisitante = trim($resultado[1]);
            }

            // Insertar en la tabla est_partidos
            Est_partido::create([
                'partido_id' => $partido->id,
                'goles_local' => $golesLocal,
                'goles_visitante' => $golesVisitante,
                'amarillas' => 0,
                'rojas' => 0
            ]);
        }
    }
}
