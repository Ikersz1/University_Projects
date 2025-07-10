<?php

namespace App\Http\Controllers;

use App\Models\Jugador;
use App\Models\Equipo;
use Illuminate\Http\Request;
use App\ServiceLayer\OrderServices;

class JugadoresController extends Controller
{
    public function index(Request $request)
    {
        try {
            $jugadores = OrderServices::getPlayersWithStatisticsAndTeams($request);

            if ($request->ajax()) {
                return response()->json([
                    'data' => $jugadores->items(),
                    'links' => $jugadores->appends($request->all())->links()->toHtml(),
                ]);
            }

            return view('jugadores.index', compact('jugadores'));
        } catch (\Exception $e) {
            return response()->json(['message' => 'Error al obtener jugadores: ' . $e->getMessage()], 500);
        }
    }

    public function show($id)
    {
        $equipos = Equipo::all();
        $jugador = Jugador::findOrFail($id);
        return view('jugadores.show', compact('jugador', 'equipos'));
}
}