<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Liga; // Asegúrate de usar el namespace correcto
use Faker\Factory as Faker;
class LigasTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
       
    
        

        
            Liga::create([
                'nombre' => 'La Liga' ,
                'pais' => 'España',
                
                'temporada'=>'2024'
            ]);
    }
}
