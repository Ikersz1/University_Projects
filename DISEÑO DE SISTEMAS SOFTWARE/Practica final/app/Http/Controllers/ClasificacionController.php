<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Equipo; // Importar el modelo Equipo

class ClasificacionController extends Controller
{
    public function index(Request $request)
    {
        // Obtener todos los equipos desde la tabla de equipos
        $equipos = Equipo::orderBy('puntos', 'desc')->get();

        // Almacena las posiciones originales de los equipos antes de ordenarlos
        $posiciones_originales = $equipos->pluck('id')->toArray();

        // Ordena los equipos según el parámetro 'order'
        $order = $request->get('order', 'puntos');
        if ($order === 'goles_favor') {
            $equipos = $equipos->sortByDesc('goles_favor');
        } elseif ($order === 'goles_contra') {
            $equipos = $equipos->sortBy('goles_contra');
        } elseif ($order === 'puntos') {
            // No necesitas ordenar nuevamente por puntos, ya están ordenados así por defecto
        }

        // Recorre los equipos ordenados y asigna las posiciones originales
        $equipos = $equipos->map(function ($equipo) use ($posiciones_originales) {
            $id = $equipo->id;
            $posicion_original = array_search($id, $posiciones_originales) + 1;
            $equipo->posicion_original = $posicion_original;
            return $equipo;
        });

        // Pasar los datos a la vista
        return view('clasificacion.index', ['equipos' => $equipos, 'order' => $order]);
    }
}
