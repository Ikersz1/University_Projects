<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Usuario;
use Faker\Factory as Faker;
class UsuariosTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        Usuario::create([
        'nombre' => 'JuaPérez',
        'correo' => 'jezhola123@example.com',
        'contraseña' => bcrypt('password')]);
    
}
}


