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
            UsuariosTableSeeder::class,
            LigasTableSeeder::class,
            EquiposTableSeeder::class,
            TitulosTableSeeder::class,
            EquipoTituloTableSeeder::class,
            JugadorsTableSeeder::class,
            EstJugadorsTableSeeder::class,
            PartidosTableSeeder::class,
            EstPartidosTableSeeder::class,
            
            
            
            
            UsuariosEquiposTableSeeder::class
        ]);
       

    }
}
