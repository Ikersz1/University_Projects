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
                'nombre' => 'REAL MADRID',
                'ganados' => 0,
                'empatados' => 0,
                'perdidos' => 0,
                'goles_favor' => 0,
                'goles_contra' => 0,
                'puntos' => 0,
                'partidos_jugados' => 0,
                'liga_id'=>'1'
            ],
            [
                'nombre' => 'FC BARCELONA',
                'ganados' => 0,
                'empatados' => 0,
                'perdidos' => 0,
                'goles_favor' => 0,
                'goles_contra' => 0,
                'puntos' => 0,
                'partidos_jugados' => 0,
                'liga_id'=>'1'
            ],
            [
                'nombre' => 'GIRONA FC',
                'ganados' => 0,
                'empatados' => 0,
                'perdidos' => 0,
                'goles_favor' => 0,
                'goles_contra' => 0,
                'puntos' => 0,
                'partidos_jugados' => 0,
                'liga_id'=>'1'
            ],
            [
                'nombre' => 'ATLÉTICO DE MADRID',
                'ganados' => 0,
                'empatados' => 0,
                'perdidos' => 0,
                'goles_favor' => 0,
                'goles_contra' => 0,
                'puntos' => 0,
                'partidos_jugados' => 0,
                'liga_id'=>'1'
            ],
            [
                'nombre' => 'ATHLETIC CLUB',
                'ganados' => 0,
                'empatados' => 0,
                'perdidos' => 0,
                'goles_favor' => 0,
                'goles_contra' => 0,
                'puntos' => 0,
                'partidos_jugados' => 0,
                'liga_id'=>'1'
            ],
            [
                'nombre' => 'REAL SOCIEDAD',
                'ganados' => 0,
                'empatados' => 0,
                'perdidos' => 0,
                'goles_favor' => 0,
                'goles_contra' => 0,
                'puntos' => 0,
                'partidos_jugados' => 0,
                'liga_id'=>'1'
            ],
            [
                'nombre' => 'VALENCIA CF',
                'ganados' => 0,
                'empatados' => 0,
                'perdidos' => 0,
                'goles_favor' => 0,
                'goles_contra' => 0,
                'puntos' => 0,
                'partidos_jugados' => 0,
                'liga_id'=>'1'
            ],
            [
                'nombre' => 'REAL BETIS',
                'ganados' => 0,
                'empatados' => 0,
                'perdidos' => 0,
                'goles_favor' => 0,
                'goles_contra' => 0,
                'puntos' => 0,
                'partidos_jugados' => 0,
                'liga_id'=>'1'
            ],
            [
                'nombre' => 'CA OSASUNA',
                'ganados' => 0,
                'empatados' => 0,
                'perdidos' => 0,
                'goles_favor' => 0,
                'goles_contra' => 0,
                'puntos' => 0,
                'partidos_jugados' => 0,
                'liga_id'=>'1'
            ],
            [
                'nombre' => 'VILLAREAL CF',
                'ganados' => 0,
                'empatados' => 0,
                'perdidos' => 0,
                'goles_favor' => 0,
                'goles_contra' => 0,
                'puntos' => 0,
                'partidos_jugados' => 0,
                'liga_id'=>'1'
            ],
            [
                'nombre' => 'GETAFE CF',
                'ganados' => 0,
                'empatados' => 0,
                'perdidos' => 0,
                'goles_favor' => 0,
                'goles_contra' => 0,
                'puntos' => 0,
                'partidos_jugados' => 0,
                'liga_id'=>'1'
            ],
            [
                'nombre' => 'UD LAS PALMAS',
                'ganados' => 0,
                'empatados' => 0,
                'perdidos' => 0,
                'goles_favor' => 0,
                'goles_contra' => 0,
                'puntos' => 0,
                'partidos_jugados' => 0,
                'liga_id'=>'1'
            ],
            [
                'nombre' => 'DEPORTIVO ALAVÉS',
                'ganados' => 0,
                'empatados' => 0,
                'perdidos' => 0,
                'goles_favor' => 0,
                'goles_contra' => 0,
                'puntos' => 0,
                'partidos_jugados' => 0,
                'liga_id'=>'1'
            ],
            [
                'nombre' => 'SEVILLA FC',
                'ganados' => 0,
                'empatados' => 0,
                'perdidos' => 0,
                'goles_favor' => 0,
                'goles_contra' => 0,
                'puntos' => 0,
                'partidos_jugados' => 0,
                'liga_id'=>'1'
            ],
            [
                'nombre' => 'RCD MALLORCA',
                'ganados' => 0,
                'empatados' => 0,
                'perdidos' => 0,
                'goles_favor' => 0,
                'goles_contra' => 0,
                'puntos' => 0,
                'partidos_jugados' => 0,
                'liga_id'=>'1'
            ],
            [
                'nombre' => 'RAYO VALLECANO',
                'ganados' => 0,
                'empatados' => 0,
                'perdidos' => 0,
                'goles_favor' => 0,
                'goles_contra' => 0,
                'puntos' => 0,
                'partidos_jugados' => 0,
                'liga_id'=>'1'
            ],
            [
                'nombre' => 'RC CELTA',
                'ganados' => 0,
                'empatados' => 0,
                'perdidos' => 0,
                'goles_favor' => 0,
                'goles_contra' => 0,
                'puntos' => 0,
                'partidos_jugados' => 0,
                'liga_id'=>'1'
            ],
            [
                'nombre' => 'CÁDIZ CF',
                'ganados' => 0,
                'empatados' => 0,
                'perdidos' => 0,
                'goles_favor' => 0,
                'goles_contra' => 0,
                'puntos' => 0,
                'partidos_jugados' => 0,
                'liga_id'=>'1'
            ],
            [
                'nombre' => 'GRANADA CF',
                'ganados' => 0,
                'empatados' => 0,
                'perdidos' => 0,
                'goles_favor' => 0,
                'goles_contra' => 0,
                'puntos' => 0,
                'partidos_jugados' => 0,
                'liga_id'=>'1'
            ],
            [
                'nombre' => 'UD ALMERÍA',
                'ganados' => 0,
                'empatados' => 0,
                'perdidos' => 0,
                'goles_favor' => 0,
                'goles_contra' => 0,
                'puntos' => 0,
                'partidos_jugados' => 0,
                'liga_id'=>'1'
            ],
        ]);
            
        
        
        
    }
}
