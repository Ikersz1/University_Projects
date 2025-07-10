<?php

namespace Database\Seeders;

use App\Models\Est_jugador;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Faker\Factory as Faker;
use App\Models\Jugador;
class EstJugadorsTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        Est_jugador::insert([
            'goles'=>'66',
            'asistencias'=>'54',
            'amarillas'=>'5',
            'rojas'=>'6',
            'jugador_id'=>'1'
        ]);
        
    }
}
