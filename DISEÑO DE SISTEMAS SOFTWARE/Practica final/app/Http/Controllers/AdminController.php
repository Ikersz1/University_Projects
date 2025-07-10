<?php



namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Jugador; // Asume que estás trabajando con un modelo 'Jugador'

class AdminController extends Controller
{
    public function index()
    {
        return view('admin.index'); // Retorna la vista de administración
    }

    public function store(Request $request)
    {
        // Validación (ajusta según tus necesidades)
        $validated = $request->validate([
            'nombre' => 'required|string|max:255',
            // Agrega aquí más reglas de validación según tus campos
        ]);

        // Crear el jugador
        Jugador::create($validated);

        // Redirigir de vuelta a la página de admin con un mensaje de éxito
        return back()->with('success', 'Jugador añadido exitosamente!');
    }
}

