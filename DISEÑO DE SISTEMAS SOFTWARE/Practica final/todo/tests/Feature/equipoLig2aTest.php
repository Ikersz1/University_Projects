<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use App\Models\Liga;
use App\Models\Equipo;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class equipoLig2aTest extends TestCase
{
    use RefreshDatabase;
    
     


public function test_example(){$response = $this->get('/');

        $response->assertStatus(200);
    }
   /** @test*/

    public function test_equipo_pertenece_a_una_liga()
    {
        $liga = Liga::create(['nombre' => 'La Liga', 'pais' => 'España', 'temporada' => '2024']);
        $equipo = Equipo::create([
            'nombre' => 'Equipo B',
            'ganados' => 3,
            'empatados' => 2,
            'perdidos' => 1,
            'goles_favor' => 10,
            'goles_contra' => 5,
            'puntos' => 10,
            'partidos_jugados' => 6,
            'liga_id' => $liga->id
        ]);

        $this->assertTrue($equipo->liga->is($liga));
    }

    /** @test*/
    public function test_liga_tiene_multiples_equipos()
    {
        $liga = Liga::create(['nombre' => 'La Liga', 'pais' => 'España', 'temporada' => '2024']);
        $equipo1 = Equipo::create([
            'nombre' => 'Equipo E',
            'ganados' => 3,
            'empatados' => 2,
            'perdidos' => 1,
            'goles_favor' => 10,
            'goles_contra' => 5,
            'puntos' => 10,
            'partidos_jugados' => 6,
            'liga_id' => $liga->id
        ]);
        $equipo2 = Equipo::create([
            'nombre' => 'Equipo F',
            'ganados' => 3,
            'empatados' => 2,
            'perdidos' => 1,
            'goles_favor' => 10,
            'goles_contra' => 5,
            'puntos' => 10,
            'partidos_jugados' => 6,
            'liga_id' => $liga->id
        ]);

        $this->assertCount(2, $liga->equipos);
    }



}
