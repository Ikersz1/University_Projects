<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;
use App\Models\Equipo;
use App\Models\Usuario;
use App\Models\Liga;
use Illuminate\Support\Facades\DB;
class EquipoUsuario extends TestCase
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
    public function test_usuario_puede_pertenecer_a_multiples_equipos()
    {
        $liga=Liga::create([
            'nombre' => 'La Liga',
            'pais' => 'España',
            'temporada'=>'2024'
        ]);

        $usuario = Usuario::create([
            'nombre' => 'Usuario 1',
            'correo' => 'kkita@example.com',
            'contraseña' => bcrypt('password')
        ]);
        $equipo1 = Equipo::create([
            'nombre' => 'Equipo I',
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
            'nombre' => 'Equipo J',
            'ganados' => 3,
            'empatados' => 2,
            'perdidos' => 1,
            'goles_favor' => 10,
            'goles_contra' => 5,
            'puntos' => 10,
            'partidos_jugados' => 6,
            'liga_id' => $liga->id
        ]);

        $usuario->equipos()->attach($equipo1->id);
        $usuario->equipos()->attach($equipo2->id);

        $this->assertCount(2, $usuario->equipos);
    }

    /** @test */
    public function test_se_puede_desasociar_equipos_de_un_usuario()
    {
        $usuario = Usuario::create([
            'nombre' => 'Usuario 1',
            'correo' => 'kkita2@example.com',
            'contraseña' => bcrypt('password')
        ]);

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
            'ganados' => 3,
            'empatados' => 2,
            'perdidos' => 1,
            'goles_favor' => 10,
            'goles_contra' => 5,
            'puntos' => 10,
            'partidos_jugados' => 6,
            'liga_id' => $liga->id
        ]);

        // Asociar los equipos al usuario
        $usuario->equipos()->attach($equipo1->id);
        $usuario->equipos()->attach($equipo2->id);

        // Desasociar un equipo
        $usuario->equipos()->detach($equipo1->id);

        // Verificar que solo queda un equipo asociado
        $this->assertCount(1, $usuario->equipos()->get());

        // Verificar que el equipo desasociado ya no está asociado
        $this->assertFalse($usuario->equipos()->where('id', $equipo1->id)->exists());
    }
}
