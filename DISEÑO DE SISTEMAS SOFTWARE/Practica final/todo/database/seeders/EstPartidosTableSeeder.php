<?php

namespace Database\Seeders;

use App\Models\Est_partido;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Faker\Factory as Faker;
use App\Models\Jugador;
class EstPartidosTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        Est_partido::create([
            'goles_local'=> '1',
            'goles_visitante'=>'3',
            'amarillas'=>5,
            'rojas'=>1,
            'partido_id'=>1



        ]);
        
    }
}
