<?php

namespace App\Http\Controllers;

use App\Models\Noticia;
use App\Models\Equipo;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class AdminNoticiasController extends Controller
{
    /**
     * Muestra la lista de noticias.
     */
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

        $noticias = $query->paginate(10);
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

        return view('admin.adminnoticias', compact('noticias','equipos'));
    }

    /**
     * Guarda una nueva noticia.
     */
    public function crear(Request $request)
    {
        $equipos = Equipo::all();
        // Validar los datos del formulario
        $validatedData = $request->validate([
            'titulo' => 'required|string|max:255',
            'contenido' => 'required|string',
            'autor' => 'nullable|string|max:255',
            'fecha' => 'nullable|date',
            'link_de_la_web' => 'nullable|string|max:255',
            'enlace_de_la_foto' => 'nullable|string|max:255',
            'equipo_id' => 'nullable|exists:equipos,id',
        ]);
        
        try{
            $noticia = Noticia::create($validatedData);
            return response()->json(['message' => 'Noticia creada con éxito', 'noticia' => $noticia], 200);
        
        }
        catch (\Exception $e) {
            return response()->json(['message' => 'Error al crear noticia: ' . $e->getMessage()], 500);
        }
 
    }

    /**
     * Muestra los datos de una noticia para editar.
     */
    public function getDatos($id)
    {
        $noticia = Noticia::findOrFail($id);
        return response()->json($noticia);
    }

    /**
     * Actualiza una noticia específica.
     */
    public function actualizar(Request $request, $id)
    {
        $noticia = Noticia::findOrFail($id);

        $validator = Validator::make($request->all(), [
            'titulo' => 'required|string|max:255',
            'contenido' => 'required|string',
            'autor' => 'nullable|string|max:255',
            'fecha' => 'nullable|date',
            'link_de_la_web' => 'nullable|string|max:255',
            'enlace_de_la_foto' => 'nullable|string|max:255',
            'equipo_id' => 'nullable|exists:equipos,id',
        ]);

        if ($validator->fails()) {
            return response()->json(['message' => 'Error en la validación', 'errors' => $validator->errors()], 422);
        }

        $noticia->update($validator->validated());

        return response()->json(['message' => 'Noticia actualizada con éxito', 'noticia' => $noticia], 200);
    }



    /**
     * Elimina una noticia específica.
     */
    public function eliminar(Request $request, $id)
    {
        $noticia = Noticia::findOrFail($id);
        $noticia->delete();
        return response()->json(['message' => 'Noticia eliminada con éxito'], 200);
    }

    public function eliminarMasa(Request $request)
    {
        $ids = $request->ids;

        try {
            Noticia::whereIn('id', $ids)->delete();

            return response()->json(['message' => 'Noticias eliminadas con éxito'], 200);
        } catch (\Exception $e) {
            return response()->json(['message' => 'Error al eliminar noticias: ' . $e->getMessage()], 500);
        }
    }

    /**
     * Elimina todas las noticias.
     */
    public function eliminarTodas()
    {
        Noticia::query()->delete();
        return response()->json(['message' => 'Todas las noticias han sido eliminadas con éxito'], 200);
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