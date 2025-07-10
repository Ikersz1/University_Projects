<?php

namespace Database\Seeders;

// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     *
     * @return void
     */
    public function run()
    {
        
        $this->call([
            RoleSeeder::class,
            UsersTableSeeder::class,
            UsuariosTableSeeder::class,
            LigasTableSeeder::class,
            EquiposTableSeeder::class,
            TitulosTableSeeder::class,
            EquipoTituloTableSeeder::class,
            JugadorsTableSeeder::class,
            EstJugadorsTableSeeder::class,
            PartidosTableSeeder::class,
            EstPartidosTableSeeder::class,
            NoticiasTableSeeder::class,
            UsersEquiposTableSeeder::class,
            UsuariosEquiposTableSeeder::class,
            PrediccionesTableSeeder::class
        ]);
       

    }
}
