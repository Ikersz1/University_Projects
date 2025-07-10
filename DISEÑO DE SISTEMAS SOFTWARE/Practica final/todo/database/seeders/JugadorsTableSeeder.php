<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Faker\Factory as Faker;
use App\Models\Jugador;
class JugadorsTableSeeder extends Seeder
{
   
public function run(){
        Jugador::insert([
        ['nombre' => 'Lionel Messi',
        'posicion' => 'Delantero',
        'nacionalidad' => 'Argentina',
        'edad' => 34,
        'equipo_id' => 1],
        ['nombre' => 'Cristiano Ronaldo'
        ,'posicion' => 'Delantero'
        ,'nacionalidad' => 'Portugal',
        'edad' => 36,
        'equipo_id' => 2]]);}
}