<?php

namespace App\Http\Controllers;

use App\Models\Noticia;
use App\Models\Equipo;
use Illuminate\Http\Request;

class NoticiasController extends Controller
{
    public function index(Request $request)
    {
        $query = Noticia::query();

        if ($request->has('search')) {
            $search = $request->search;
            $query->where('titulo', 'LIKE', "%{$search}%")
                ->orWhere('id', 'LIKE', "%{$search}%")
                ->orWhereHas('equipo', function ($query) use ($search) {
                    $query->where('nombre', 'LIKE', "%{$search}%");
                });
        }


        $noticias = $query->paginate(5);
        $equipos = Equipo::all();

        if ($request->ajax()) {
            // Preparando el HTML de los enlaces de paginación
            $links = $noticias->appends(['search' => $search])->links()->toHtml();

            // Preparando los datos para enviar
            return response()->json([
                'data' => $noticias->items(), // Datos de jugadores
                'links' => $links, // HTML de los enlaces de paginación
            ]);
        }

        return view('noticias', compact('noticias','equipos'));
    }

    public function getDatos($id)
    {
        $noticia = Noticia::findOrFail($id);
        return response()->json($noticia);
    }
    public function getEquipoName($id)
    {
        // Busca el equipo por su ID
        $equipo = Equipo::find($id);

        // Verifica si se encontró el equipo
        if ($equipo) {
            // Devuelve el nombre del equipo en formato JSON
            return response()->json(['nombre' => $equipo->nombre]);
        } else {
            // Devuelve un mensaje de error si el equipo no se encuentra
            return response()->json(['error' => 'Equipo no encontrado'], 404);
        }
    }
}