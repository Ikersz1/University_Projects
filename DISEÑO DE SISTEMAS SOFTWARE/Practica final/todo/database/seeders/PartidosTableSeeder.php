<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Faker\Factory as Faker;
use App\Models\Partido;
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
        Partido::create([
        'fecha' => Carbon::create('2023', '01', '01')->format('Y-m-d'),
                'hora' => Carbon::createFromTimeString('15:00:00')->format('H:i:s'),
                'estadio' => 'Estadio Nacional',
                'resultado' => '2-1',
                'equipo_local_id' => 1,
                'equipo_visitante_id' => 2
            ],
            
        );
    }
}
