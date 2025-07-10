<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Role;
class RoleSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        Role::create(['name' => 'user', 'description' => 'Rol de usuario']);
        Role::create(['name' => 'admin', 'description' => 'Rol de admin, acceso a todo']);
        Role::create(['name' => 'noticiero', 'description' => 'Rol para crear noticias']);
        Role::create(['name' => 'analista', 'description' => 'Rol para crear jugadores , equipos y partidos']);
    }
}
