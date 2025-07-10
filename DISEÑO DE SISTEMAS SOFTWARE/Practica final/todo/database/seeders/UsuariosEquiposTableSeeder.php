<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Usuario;
use App\Models\Equipo;
use Illuminate\Support\Facades\DB;
class UsuariosEquiposTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table('usuarios_equipos')->insert([
            ['usuario_id' => 1, 'equipo_id' => 1]

        ]);
    }
}
