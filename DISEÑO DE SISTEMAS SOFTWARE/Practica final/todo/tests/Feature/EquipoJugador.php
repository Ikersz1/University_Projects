<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;
use App\Models\Equipo;
use App\Models\Jugador;
use App\Models\Est_Jugador;
use Illuminate\Support\Facades\DB;
class EquipoLiga extends TestCase
{
    use RefreshDatabase;
    /**
     * A basic feature test example.
     *
     * @return void
     */
    public function test_example()
    {
        $response = $this->get('/');

        $response->assertStatus(200);
    }
    /** @test */
    public function test_equipo_tiene_multiples_jugadores()
    {
        $equipo = Equipo::create(['nombre' => 'Equipo C']);
        $jugador1 = Jugador::create(['nombre' => 'Jugador 1', 'equipo_id' => $equipo->id]);
        $jugador2 = Jugador::create(['nombre' => 'Jugador 2', 'equipo_id' => $equipo->id]);

        $this->assertCount(2, $equipo->jugadores);
    }

    /** @test */
    public function test_jugador_pertenece_a_un_equipo_y_tiene_estadisticas()
    {
        $equipo = Equipo::create(['nombre' => 'Equipo D']);
        $jugador = Jugador::create(['nombre' => 'Jugador 3', 'equipo_id' => $equipo->id]);
        $estadisticas = Est_Jugador::create(['jugador_id' => $jugador->id]);

        $this->assertTrue($jugador->equipo->is($equipo));
        $this->assertTrue($jugador->estadisticas->is($estadisticas));
    }


}
