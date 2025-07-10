<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\User;

class UsersTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        User::insert([
            [
                'name' => 'admin',
                'email' => 'admin@example.com',
                'password' => bcrypt('admin1234'),
               
                'role_id'=>'2'
            ],
            [
                'name' => 'admin2',
                'email' => 'admin2@example.com',
                'password' => bcrypt('admin12345'),
                
                'role_id'=>'2'
            ],
            [
                'name' => 'prueba',
                'email' => 'prueba@example.com',
                'password' => bcrypt('prueba1234'),
                
                'role_id'=>'1'
            ],
            [
                'name' => 'notici',
                'email' => 'noticiero@example.com',
                'password' => bcrypt('prueba1234'),
                
                'role_id'=>'3'
            ],
            [
                'name' => 'analis',
                'email' => 'analista@example.com',
                'password' => bcrypt('prueba1234'),
                
                'role_id'=>'4'
            ]
        ]);

        User::factory()->count(9)->create();
    }
}
