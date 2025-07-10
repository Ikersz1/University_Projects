<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Faker\Factory as Faker;
use App\Models\Equipo;
class EquiposTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        Equipo::insert([
            [
                'nombre' => 'Equipo A',
                'ganados' => 3,
                'empatados' => 2,
                'perdidos' => 1,
                'goles_favor' => 10,
                'goles_contra' => 5,
                'puntos' => 11,
                'partidos_jugados' => 6,
                'liga_id'=>'1'
            ],
            [
                'nombre' => 'Equipo B',
                'ganados' => 2,
                'empatados' => 3,
                'perdidos' => 1,
                'goles_favor' => 8,
                'goles_contra' => 6,
                'puntos' => 9,
                'partidos_jugados' => 6,
                'liga_id'=>'1'
            ]
        ]);
            
        
        
        
    }
}
