<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;
use App\Models\Equipo;
use App\Models\Titulo;
use App\Models\Liga;
use Illuminate\Support\Facades\DB;
class EquipoTest extends TestCase
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
    public function test_un_equipo_puede_tener_multiples_titulos()
    {
       
        $liga=Liga::create([
            'nombre' => 'La Liga' ,
            'pais' => 'España',
            'temporada'=>'2024'
        ]);
        $equipo = Equipo::create([
        
        'nombre' => 'Equipo A',
        'ganados' => 3,
        'empatados' => 2,
        'perdidos' => 1,
        'goles_favor' => 10,
        'goles_contra' => 5,
        'puntos' => 11,
        'partidos_jugados' => 6,
        'liga_id' => $liga->id]);
        
        $titulo1 = Titulo::create(['nombre' => 'Titulo 1', 'temporada' => '2020']);
        $titulo2 = Titulo::create(['nombre' => 'Titulo 2', 'temporada' => '2021']);
        
        $equipo->titulos()->attach($titulo1->id);
        $equipo->titulos()->attach($titulo2->id);

        $this->assertCount(2, $equipo->titulos);

        /* $titulo2->delete();
        $titulo1->delete();
        $equipo->delete();
        $liga->delete(); */
    }
    public function test_se_puede_desasociar_un_titulo_de_un_equipo()
    {
        $liga=Liga::create([
            'nombre' => 'La Liga' ,
            'pais' => 'España',
            'temporada'=>'2024'
        ]);
        $equipo = Equipo::create([
            
            'nombre' => 'Equipo B',
            'ganados' => 2,
            'empatados' => 3,
            'perdidos' => 1,
            'goles_favor' => 8,
            'goles_contra' => 6,
            'puntos' => 9,
            'partidos_jugados' => 6,
            'liga_id' => $liga->id
        ]);
        $titulo = Titulo::create(['nombre' => 'Titulo 1', 'temporada' => '2020']);

        $equipo->titulos()->attach($titulo->id);
        $this->assertCount(1, $equipo->titulos);

        $equipo->titulos()->detach($titulo->id);
        $this->assertCount(0, $equipo->refresh()->titulos);

        /* $titulo->delete();
        $equipo->delete();
        $liga->delete(); */
    }
}
