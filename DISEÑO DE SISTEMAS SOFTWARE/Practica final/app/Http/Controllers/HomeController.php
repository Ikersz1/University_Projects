<?php

namespace App\Http\Controllers;

use App\Models\Equipo;
use App\Models\Noticia;
use App\Models\Partido; // Asegúrate de que este modelo existe y está correctamente configurado
use Illuminate\Http\Request;

class HomeController extends Controller
{
    public function index()
    {
        // Obtener las últimas noticias
        $noticias = Noticia::latest()->take(5)->get(); // Asume que tienes una tabla/modelo de noticias

        // Obtener la clasificación de equipos
        $equipos = Equipo::orderBy('puntos', 'desc')->get(); // Ordena los equipos por puntos de forma descendente

        // Obtener los próximos partidos
        $partidos = Partido::with(['equipoLocal', 'equipoVisitante'])
                            ->where('fecha', '>', now()->toDateString())
                            ->orderBy('fecha', 'asc')
                            ->orderBy('hora', 'asc')
                            ->take(5) // Número de próximos partidos a mostrar
                            ->get();

        // Pasar los datos a la vista
        return view('home', compact('noticias', 'equipos', 'partidos'));
    }
}

