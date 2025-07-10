<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use App\Models\Equipo;
use App\Models\Titulo;
class EquipoTituloTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table('equipo_titulo')->insert([
            'equipo_id'=>'1',
            'titulo_id'=>'1'


        ]);
    }
}
