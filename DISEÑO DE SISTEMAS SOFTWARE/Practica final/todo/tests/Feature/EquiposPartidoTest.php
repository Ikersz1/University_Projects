<?php

namespace Tests\Feature;
use App\Models\Est_partido;
use Tests\TestCase;
use App\Models\Equipo;
use App\Models\Partido;
use App\Models\Liga;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;


class EquiposPartidoTest extends TestCase
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

    public function test_relacion_equipos_partido_estadisticas(){
        $liga=Liga::create([
            'nombre' => 'La Liga',
            'pais' => 'España',
            'temporada'=>'2024'
        ]);

        $equipoLocal = Equipo::create([
            'nombre' => 'Equipo Local',
            'ganados' => 3,
            'empatados' => 2,
            'perdidos' => 1,
            'goles_favor' => 10,
            'goles_contra' => 5,
            'puntos' => 10,
            'partidos_jugados' => 6,
            'liga_id' => $liga->id
        ]);

        $equipoVisitante = Equipo::create([
            'nombre' => 'Equipo Visitante',
            'ganados' => 3,
            'empatados' => 2,
            'perdidos' => 1,
            'goles_favor' => 10,
            'goles_contra' => 5,
            'puntos' => 10,
            'partidos_jugados' => 6,
            'liga_id' => $liga->id
        ]);


        $partido = Partido::create([ 
            'fecha'=> '2023-01-01',
            'hora' => '15:00:00',
            'estadio'=> 'Estadio Nacional',
            'resultado'=> '2-1',
            'equipo_local_id'=> $equipoLocal->id,
            'equipo_visitante_id'=> $equipoVisitante->id,
            
        ]);

        $estadisticas = Est_partido::create([
        'partido_id' => $partido->id,
        'goles_local'=> '1',
            'goles_visitante'=>'3',
            'amarillas'=>5,
            'rojas'=>1
            
        ]);

        $this->assertInstanceOf(Partido::class, $partido);
        $this->assertEquals($equipoLocal->id, $partido->equipoLocal->id);
        $this->assertEquals($equipoVisitante->id, $partido->equipoVisitante->id);
        
        $this->assertEquals($partido->equipoLocal->nombre, 'Equipo Local');
        $this->assertEquals($partido->equipoVisitante->nombre, 'Equipo Visitante');
        $this->assertTrue($partido->estadisticas->is($estadisticas));

        /*$partido->delete();
        $equipoLocal->delete();
        $equipoVisitante->delete();
        $liga->delete();*/

    }

    public function test_equipoLocal_distinto_equipoVisitante(){
        $liga=Liga::create([
            'nombre' => 'La Liga',
            'pais' => 'España',
            'temporada'=>'2024'
        ]);

        $equipo1 = Equipo::create([
            'nombre' => 'Equipo 1',
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
            'nombre' => 'Equipo 2',
            'ganados' => 2,
            'empatados' => 3,
            'perdidos' => 1,
            'goles_favor' => 8,
            'goles_contra' => 6,
            'puntos' => 9,
            'partidos_jugados' => 6,
            'liga_id' => $liga->id
        ]);

        $partido = Partido::create([ 
            'fecha'=> '2024-03-01',
            'hora' => '20:50:00',
            'estadio'=> 'santiago Bernabeu',
            'resultado'=> '3-2',
            'equipo_local_id'=> $equipo1->id,
            'equipo_visitante_id'=> $equipo2->id
        ]);


        $this->assertTrue($partido->equipoLocal() != $partido->equipoVisitante());

        /* $partido->delete();
        $equipo1->delete();
        $equipo2->delete();
        $liga->delete(); */
    }
}
