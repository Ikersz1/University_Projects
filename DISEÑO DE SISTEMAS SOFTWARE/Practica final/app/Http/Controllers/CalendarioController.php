<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;
use App\Models\Partido;
use App\Models\Est_partido;
use App\Models\Equipo;
use App\Models\Prediccion;

class CalendarioController extends Controller
{
    public function index($jornada = 1)
    {
        $totalJornadas = 38;
        $jornadas = range(1, $totalJornadas);

        if ($jornada < 1) {
            $jornada = $totalJornadas; // Ir a la última jornada si se va más allá de la primera
        } elseif ($jornada > $totalJornadas) {
            $jornada = 1; // Ir a la primera jornada si se va más allá de la última
        }

        $jornada_actual = $jornada;

        // Obtener los partidos de la jornada seleccionada con 
        // relaciones de equipo cargadas
        $partidos = Partido::where('jornada', $jornada)
            ->with('equipoLocal', 'equipoVisitante')
            ->orderBy('fecha')
            ->orderBy('hora')
            ->get();

        Carbon::setLocale('es');

        foreach ($partidos as $partido) {
            $fecha = Carbon::createFromFormat('Y-m-d', $partido->fecha);

            $hora = Carbon::createFromFormat('H:i:s', $partido->hora);
            $partido->hora_nueva = $hora->format('H:i');

            if ($fecha->isToday()) {
                $partido->fecha_nueva = 'Hoy';
            } elseif ($fecha->isTomorrow()) {
                $partido->fecha_nueva = 'Mañana';
            } else {
                $partido->fecha_nueva = $fecha->isoFormat('ddd D MMM YYYY');
            }
        }

        $equipos = Equipo::all();

        return view('calendario.index', compact('partidos', 'jornada_actual', 'jornadas', 'equipos'));
    }

    public function show($equipo)
    {
        // $equipo aquí es el ID del equipo seleccionado
        $equipo_id = $equipo;

        // Obtener información del equipo seleccionado
        $equipo = Equipo::findOrFail($equipo_id);

        // Obtener partidos donde el equipo es local o visitante
        $partidos = Partido::where('equipo_local_id', $equipo_id)
            ->orWhere('equipo_visitante_id', $equipo_id)
            ->orderBy('jornada')
            ->get();

        Carbon::setLocale('es');

        foreach ($partidos as $partido) {
            $fecha = Carbon::createFromFormat('Y-m-d', $partido->fecha);

            $hora = Carbon::createFromFormat('H:i:s', $partido->hora);
            $partido->hora_nueva = $hora->format('H:i');

            if ($fecha->isToday()) {
                $partido->fecha_nueva = 'Hoy';
            } elseif ($fecha->isTomorrow()) {
                $partido->fecha_nueva = 'Mañana';
            } else {
                $partido->fecha_nueva = $fecha->isoFormat('ddd D MMM YYYY');
            }
        }
        
        $equipos = Equipo::all();

        return view('calendario.show', compact('equipos', 'partidos'));
    }

    public function showEstadisticas($id)
    {
        Carbon::setLocale('es');

        $partido = Partido::findOrFail($id);
        $usuario = Auth::user();
        if($usuario != null){
            $haVotado = Prediccion::where('user_id', $usuario->id)
                                ->where('partido_id', $partido->id)
                                ->exists();

            $fecha = Carbon::createFromFormat('Y-m-d', $partido->fecha);
            $partido->fecha_nueva = $fecha->isoFormat('D [de] MMMM [de] YYYY[, ]');
    
            $hora = Carbon::createFromFormat('H:i:s', $partido->hora);
            $partido->hora_nueva = $hora->format('H:i');
    
            $estPartido = Est_partido::where('partido_id', $id)->first();
    
            // Obtener los conteos de votos para cada opción del partido
            $totalVotos = Prediccion::where('partido_id', $partido->id)->count();
            $votosLocal = Prediccion::where('partido_id', $partido->id)->where('voto_local', 1)->count();
            $votosVisitante = Prediccion::where('partido_id', $partido->id)->where('voto_visitante', 1)->count();
            $votosEmpate = Prediccion::where('partido_id', $partido->id)->where('voto_empate', 1)->count();
    
            // Calcular porcentajes de votos
            $porcentajeLocal = ($totalVotos > 0) ? ($votosLocal / $totalVotos) * 100 : 0;
            $porcentajeVisitante = ($totalVotos > 0) ? ($votosVisitante / $totalVotos) * 100 : 0;
            $porcentajeEmpate = ($totalVotos > 0) ? ($votosEmpate / $totalVotos) * 100 : 0;

            // Formatear porcentajes con máximo dos decimales
            $porcentajeLocalFormatted = ($porcentajeLocal == round($porcentajeLocal)) ? round($porcentajeLocal) : number_format($porcentajeLocal, 2);
            $porcentajeVisitanteFormatted = ($porcentajeVisitante == round($porcentajeVisitante)) ? round($porcentajeVisitante) : number_format($porcentajeVisitante, 2);
            $porcentajeEmpateFormatted = ($porcentajeEmpate == round($porcentajeEmpate)) ? round($porcentajeEmpate) : number_format($porcentajeEmpate, 2);

            return view('partidos', compact('partido', 'estPartido', 'haVotado', 
                        'porcentajeLocalFormatted', 'porcentajeEmpateFormatted', 'porcentajeVisitanteFormatted', 
                        'totalVotos'));
        }
        else{
            $fecha = Carbon::createFromFormat('Y-m-d', $partido->fecha);
            $partido->fecha_nueva = $fecha->isoFormat('D [de] MMMM [de] YYYY[, ]');

            $hora = Carbon::createFromFormat('H:i:s', $partido->hora);
            $partido->hora_nueva = $hora->format('H:i');

            $estPartido = Est_partido::where('partido_id', $id)->first();

            return view('partidos', compact('partido', 'estPartido'));
        }
    }

    public function guardarVoto(Request $request)
    {
        $partidoId = $request->input('partido_id');
        $opcion = $request->input('opcion');

        $usuario = Auth::user();

        $prediccion = new Prediccion();
        $prediccion->user_id = $usuario->id;
        $prediccion->partido_id = $partidoId;

        if ($opcion === 'local') {
            $prediccion->voto_local = 1;
            $prediccion->voto_empate = 0;
            $prediccion->voto_visitante = 0;
        } elseif ($opcion === 'empate') {
            $prediccion->voto_local = 0;
            $prediccion->voto_empate = 1;
            $prediccion->voto_visitante = 0;
        } elseif ($opcion === 'visitante') {
            $prediccion->voto_local = 0;
            $prediccion->voto_empate = 0;
            $prediccion->voto_visitante = 1;
        }
        $prediccion->save();

        return response()->json(['success' => true]);
    }
}
