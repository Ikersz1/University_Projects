<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\User;
use App\Models\Equipo;
use Illuminate\Support\Facades\DB;

class UsersEquiposTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table('users_equipos')->insert([
            ['user_id' => 1, 'equipo_id' => 1],
            ['user_id' => 1, 'equipo_id' => 2],
            ['user_id' => 1, 'equipo_id' => 3],
            ['user_id' => 1, 'equipo_id' => 4],
            ['user_id' => 1, 'equipo_id' => 5]

        ]);
    }
}
