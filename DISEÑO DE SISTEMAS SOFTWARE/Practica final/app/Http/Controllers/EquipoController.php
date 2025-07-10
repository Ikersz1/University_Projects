<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Auth;
use App\Models\Equipo;
use Illuminate\Http\Request;

class EquipoController extends Controller
{
    public function index(Request $request)
    {
        $equipos = Equipo::all();

        return view('equipos.index', compact('equipos'));
    }


    public function show(Equipo $equipo)
    {
        return view('equipos.show', compact('equipo'));
    }

    public function agregarFavorito(Request $request, Equipo $equipo)
    {
        $user = Auth::user();

        if ($user) {
            $user->equipos()->attach($equipo->id);
            return redirect()->route('equipos.index');
        } else {
            return redirect()->route('equipos.index');
        }
    }

    public function eliminarFavorito(Request $request, Equipo $equipo)
    {
        $user = Auth::user();

        if ($user) {
            $user->equipos()->detach($equipo->id);
            return redirect()->route('equipos.index');
        } else {
            return redirect()->route('equipos.index');
        }
    }
}
