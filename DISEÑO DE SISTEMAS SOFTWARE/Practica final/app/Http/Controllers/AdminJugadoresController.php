<?php

namespace App\Http\Controllers;

use App\Models\Equipo;
use App\Models\Jugador;
use App\Models\Est_jugador;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;

class AdminJugadoresController extends Controller
{
    public function index(Request $request)
    {
        $equipos = Equipo::all();
        $query = Jugador::query();
    
        if ($request->has('search')) {
            $search = $request->search;
            $query->where('nombre', 'LIKE', "%{$search}%");
        }
    
        $jugadores = $query->paginate(10); // Paginación
    
        if ($request->ajax()) {
            $links = $jugadores->appends(['search' => $search])->links()->toHtml();
            return response()->json([
                'data' => $jugadores->items(),
                'links' => $links,
            ]);
        }
    
        return view('admin.adminjugador', compact('jugadores','equipos'));
    }

    public function eliminar(Request $request, $id)
    {
        try {
            $jugador = Jugador::findOrFail($id);
            if ($jugador->foto) {
                Storage::disk('public')->delete($jugador->foto);
            }
            $jugador->delete();
            return response()->json(['message' => 'Jugador eliminado con éxito'], 200);
        } catch (\Exception $e) {
            return response()->json(['message' => 'Error al eliminar jugador'], 500);
        }
    }

    public function eliminarMasa(Request $request)
    {
        $ids = $request->ids;

        try {
            $jugadores = Jugador::whereIn('id', $ids)->get();
            foreach ($jugadores as $jugador) {
                if ($jugador->foto) {
                    Storage::disk('public')->delete($jugador->foto);
                }
                $jugador->delete();
            }
            return response()->json(['message' => 'Jugadores eliminados con éxito'], 200);
        } catch (\Exception $e) {
            return response()->json(['message' => 'Error al eliminar jugadores: ' . $e->getMessage()], 500);
        }
    }

    public function editar($id)
    {
        $equipos = Equipo::all();
        $jugador = Jugador::findOrFail($id);
        return view('editar', compact('jugador', 'equipos'));
    }
    
    public function create()
    {
        $equipos = Equipo::all();
        return view('crear', compact('equipos'));
    }
    public function crear(Request $request)
{
    Log::info($request->all());
    
    $validatedData = $request->validate([
        'nombre' => 'required|string|max:255',
        'posicion' => 'string|max:255|nullable',
        'nacionalidad' => 'string|max:255|nullable',
        'edad' => 'integer|nullable',
        'equipo_id' => 'required|exists:equipos,id',
        'foto' => 'nullable|image|mimes:jpeg,png,jpg,gif,svg|max:2048', // Validación para la imagen
        'biografia' => 'string|nullable',
    ]);

    if ($request->hasFile('foto')) {
        $foto = $request->file('foto');
        $path = $foto->store('images', 'public');
        $validatedData['foto'] = $path;
    }

    try {
        $jugador = Jugador::create($validatedData);
        return response()->json([
            'message' => 'Jugador creado con éxito',
            'jugador' => $jugador,
        ], 200);
    } catch (\Exception $e) {
        return response()->json(['message' => 'Error al crear jugador: ' . $e->getMessage()], 500);
    }
}
    public function store(Request $request)
{
    $validatedData = $request->validate([
        'nombre' => 'required|string|max:255',
        'posicion' => 'string|max:255|nullable',
        'nacionalidad' => 'string|max:255|nullable',
        'edad' => 'integer|nullable',
        'equipo_id' => 'required|exists:equipos,id',
        'foto' => 'nullable|image|mimes:jpeg,png,jpg,gif,svg|max:2048',
        'biografia' => 'string|nullable',
    ]);

    if ($request->hasFile('foto')) {
        $path = $request->file('foto')->store('images/jugadores', 'public');
        $validatedData['foto'] = $path;
    }

    try {
        $jugador = Jugador::create($validatedData);

        Est_jugador::create([
            'jugador_id' => $jugador->id,
            'goles' => 0,
            'asistencias' => 0,
            'amarillas' => 0,
            'rojas' => 0,
        ]);

        return redirect()->route('adminjugador.index')->with('success', 'Jugador creado con éxito.');
    } catch (\Exception $e) {
        return back()->withInput()->with('error', 'Error al crear jugador: ' . $e->getMessage());
    }
}

public function actualizar(Request $request, $id)
{
    $request->validate([
        'nombre' => 'required|string|max:255',
        'posicion' => 'nullable|string|max:255',
        'nacionalidad' => 'nullable|string|max:255',
        'edad' => 'nullable|integer',
        'equipo_id' => 'required|exists:equipos,id',
        'foto' => 'nullable|image|mimes:jpeg,png,jpg,gif,svg|max:2048',
        'biografia' => 'nullable|string',
        'goles' => 'required|integer',
        'asistencias' => 'required|integer',
        'amarillas' => 'required|integer',
        'rojas' => 'required|integer',
    ]);

    $jugador = Jugador::findOrFail($id);

    $data = $request->only(['nombre', 'posicion', 'nacionalidad', 'edad', 'equipo_id', 'biografia']);

    if ($request->hasFile('foto')) {
        if ($jugador->foto) {
            Storage::disk('public')->delete($jugador->foto);
        }
        $path = $request->file('foto')->store('images/jugadores', 'public');
        $data['foto'] = $path;
    }

    try {
        $jugador->update($data);

        $jugador->estadisticas->update($request->only(['goles', 'asistencias', 'amarillas', 'rojas']));

        return redirect()->route('adminjugador.index')->with('success', 'Jugador actualizado correctamente.');
    } catch (\Exception $e) {
        return back()->withInput()->with('error', 'Error al actualizar el jugador: ' . $e->getMessage());
    }
}


    public function getDatos($id)
    {
        $jugador = Jugador::findOrFail($id);
        return response()->json($jugador);
    }

    
    public function eliminarTodos()
    {
        try {
            $jugadores = Jugador::all();
            foreach ($jugadores as $jugador) {
                if ($jugador->foto) {
                    Storage::disk('public')->delete($jugador->foto);
                }
                $jugador->delete();
            }
            return response()->json(['message' => 'Todos los jugadores y estadísticas han sido eliminados con éxito'], 200);
        } catch (\Exception $e) {
            return response()->json(['message' => 'Error al eliminar todos los jugadores: ' . $e->getMessage()], 500);
        }
    }

    public function insertarJugadores()
    {
        try {
            \Artisan::call('db:seed', ['--class' => 'JugadorsTableSeeder']);
            return response()->json(['message' => 'Jugadores insertados correctamente'], 200);
        } catch (\Exception $e) {
            return response()->json(['message' => 'Error al insertar jugadores: ' . $e->getMessage()], 500);
        }
}
}
