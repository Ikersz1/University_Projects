<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;
use App\Models\Equipo;
use App\Models\Jugador;
use App\Models\Est_jugador;
use App\Models\Liga;
use Illuminate\Support\Facades\DB;

class EquipoLiga extends TestCase
{
    use RefreshDatabase;

    public function test_equipo_tiene_multiples_jugadores()
    {
        $liga = Liga::create([
            'nombre' => 'La Liga',
            'pais' => 'España',
            'temporada' => '2024'
        ]);

        $equipo = Equipo::create([
            'nombre' => 'Equipo C',
            'ganados' => 3,
            'empatados' => 2,
            'perdidos' => 1,
            'goles_favor' => 10,
            'goles_contra' => 5,
            'puntos' => 11,
            'partidos_jugados' => 6,
            'liga_id' => $liga->id // Aquí debes usar $liga->id en lugar de '1'
        ]);

        $jugador1 = Jugador::create([
            'nombre' => 'Jugador 1',
            'equipo_id' => $equipo->id,
        ]);

        $jugador2 = Jugador::create([
            'nombre' => 'Jugador 2',
            'equipo_id' => $equipo->id,
            'posicion' => 'Delantero',
            'nacionalidad' => 'Argentina',
            'edad' => 34
        ]);

        $this->assertCount(2, $equipo->jugadores);
    }

    public function test_jugador_pertenece_a_un_equipo_y_tiene_estadisticas()
    {
        $liga = Liga::create([
            'nombre' => 'La Liga',
            'pais' => 'España',
            'temporada' => '2024'
        ]);

        $equipo = Equipo::create([
            'nombre' => 'Equipo D',
            'ganados' => 3,
            'empatados' => 2,
            'perdidos' => 1,
            'goles_favor' => 10,
            'goles_contra' => 5,
            'puntos' => 11,
            'partidos_jugados' => 6,
            'liga_id' => $liga->id // Aquí debes usar $liga->id en lugar de '1'
        ]);

        $jugador = Jugador::create([
            'nombre' => 'Jugador 3',
            'equipo_id' => $equipo->id,
            'posicion' => 'Delantero',
            'nacionalidad' => 'Argentina',
            'edad' => 34
        ]);

        $estadisticas = Est_jugador::create([
            'jugador_id' => $jugador->id,
            
            'goles'=>'66',
            'asistencias'=>'54',
            'amarillas'=>'5',
            'rojas'=>'6'
            
        ]);

        $this->assertTrue($jugador->equipo->is($equipo));
        $this->assertTrue($jugador->estadisticas->is($estadisticas));
    }
}

