<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Session;
use Carbon\Carbon;
use App\Models\Equipo;
use App\Models\Partido;
use App\Models\Est_partido;

class AdminPartidoController extends Controller
{
    public function index(Request $request)
    {
        $jornadaSeleccionada = $request->input('jornada', 1);

        $partidos = Partido::where('jornada', $jornadaSeleccionada)->get();

        Carbon::setLocale('es');

        foreach ($partidos as $partido) {
            $fecha = Carbon::createFromFormat('Y-m-d', $partido->fecha);
            $partido->fecha_nueva = $fecha->isoFormat('ddd D MMM YYYY');

            $hora = Carbon::createFromFormat('H:i:s', $partido->hora);
            $partido->hora_nueva = $hora->format('H:i');
        }
        $totalJornadas = 38;
        $jornadas = range(1, $totalJornadas);
        $jornada_actual = $request->jornada;

        $equipos = Equipo::all();

        return view('admin.partidos.index', compact('partidos', 'jornadaSeleccionada', 'jornadas', 'jornada_actual', 'equipos'));
    }


    public function create()
    {
        $equipos = Equipo::all();

        return view('admin.partidos.create', compact('equipos'));
    }

    public function store(Request $request)
    {
        $jornada = $request->input('jornada');
        $partidosEnJornada = Partido::where('jornada', $jornada)->count();

        if ($partidosEnJornada >= 10) {
            Session::flash('success', 'Ya hay 10 partidos en esta jornada.');
            return redirect()->back();
        }

        $equipoLocal = $request->input('equipo_local');
        $partidoEquipoLocal = Partido::where('jornada', $jornada)
            ->where('equipo_local_id', $equipoLocal)
            ->first();

        if ($partidoEquipoLocal) {
            Session::flash('success', 'Este equipo ya existe en un partido de esta jornada.');
            return redirect()->back();
        }

        $equipoVisitante = $request->input('equipo_visitante');
        $partidoEquipoVisitante = Partido::where('jornada', $jornada)
            ->where('equipo_visitante_id', $equipoVisitante)
            ->first();

        if ($partidoEquipoVisitante) {
            Session::flash('success', 'Este equipo ya existe en un partido de esta jornada.');
            return redirect()->back();
        }

        $validator = Validator::make($request->all(), [
            'fecha' => [
                'required',
                'date',
                function ($attribute, $value, $fail) {
                    $minDate = Carbon::createFromFormat('Y-m-d', '2023-08-10');
                    $selectedDate = Carbon::createFromFormat('Y-m-d', $value);
                    if ($selectedDate <= $minDate) {
                        Session::flash('success', 'La fecha debe ser posterior al 10 de agosto de 2023.');
                    }
                },
            ],
            'hora' => 'required',
            'estadio' => 'required',
            'resultado' => [
                'required',
                function ($attribute, $value, $fail) {
                    $cleanedValue = str_replace(' ', '', $value); // Elimina los espacios en blanco
                    if ($cleanedValue !== '-' && !preg_match('/^\d+-\d+$/', $cleanedValue)) {
                        $fail('El campo debe tener el formato adecuado (número-número o -).');
                    }
                }
            ],
            'equipo_local' => 'required|different:equipo_visitante',
            'equipo_visitante' => 'required|different:equipo_local',
        ]);

        if ($validator->fails()) {

            return redirect()->route('admin.partidos.create');
        }

        $maxId = Partido::max('id');
        $nextId = $maxId + 1;


        $partido = new Partido();
        $partido->id = $nextId;
        $partido->fecha = $request->fecha;
        $partido->hora = $request->hora;
        $partido->estadio = $request->estadio;
        $partido->equipo_local_id = $request->equipo_local;
        $partido->resultado = $request->resultado;
        $partido->equipo_visitante_id = $request->equipo_visitante;
        $partido->jornada = $request->jornada;
        $partido->save();

        $resultado = $partido->resultado;

        if ($resultado === '-') {
            $goles_local = 0;
            $goles_visitante = 0;
        } else {
            list($goles_local, $goles_visitante) = explode('-', $resultado);
            $goles_local = trim($goles_local);
            $goles_visitante = trim($goles_visitante);
        }

        $maxIdEst = Est_partido::max('id');
        $nextIdEst = $maxIdEst + 1;

        $estPartido = new Est_partido();
        $estPartido->id = $nextIdEst;
        $estPartido->partido_id = $partido->id;
        $estPartido->goles_local = trim($goles_local);
        $estPartido->goles_visitante = trim($goles_visitante);
        $estPartido->amarillas = 0;
        $estPartido->rojas = 0;

        $estPartido->save();

        return redirect()->route('admin.partidos.index')->with('success', 'Partido creado exitosamente.');
    }

    public function edit($id)
    {
        $partido = Partido::findOrFail($id);

        $equipos = Equipo::all();
        return view('admin.partidos.edit', compact('equipos', 'partido'));
    }

    public function show($equipo)
    {
        $equipo_id = $equipo;

        $equipo = Equipo::findOrFail($equipo_id);

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

        return view('admin.partidos.show', compact('equipos', 'partidos'));
    }

    public function update(Request $request, $id)
    {
        $partido = Partido::findOrFail($id);

        $validator = Validator::make($request->all(), [
            'fecha' => 'required|date',
            'hora' => ['required', 'regex:/^(0[0-9]|1[0-9]|2[0-3]):[0-5][0-9]:[0-5][0-9]$/'],
            'estadio' => 'required',
            'equipo_local' => 'required|different:equipo_visitante',
            'resultado' => [
                'required',
                function ($attribute, $value, $fail) {
                    $cleanedValue = str_replace(' ', '', $value); // Elimina los espacios en blanco
                    if ($cleanedValue !== '-' && !preg_match('/^\d+-\d+$/', $cleanedValue)) {
                        $fail('El campo debe tener el formato adecuado (numero-numero o -).');
                    }
                }
            ],
            'equipo_visitante' => 'required|different:equipo_local',
            'jornada' => 'required|integer|min:1|max:38',
        ], [
            'hora.regex' => 'El formato de la hora es inválido. Utiliza el formato HH:mm:ss (24 horas).',
            'resultado.regex' => 'El campo resultado debe tener el formato adecuado (número - número) ó (-).',
        ]);

        if ($validator->fails()) {
            return redirect()->back()->withErrors($validator)->withInput();
        }

        $partido->fecha = $request->input('fecha');
        $partido->hora = $request->hora;
        $partido->estadio = $request->estadio;
        $partido->resultado = $request->resultado;
        $partido->jornada = $request->jornada;
        $partido->equipo_local_id = $request->equipo_local;
        $partido->equipo_visitante_id = $request->equipo_visitante;
        $partido->save();


        $resultado = $partido->resultado;

        if ($resultado === '-') {
            $goles_local = 0;
            $goles_visitante = 0;
        } else {
            list($goles_local, $goles_visitante) = explode('-', $resultado);
            $goles_local = trim($goles_local);
            $goles_visitante = trim($goles_visitante);
        }

        $maxIdEst = Est_partido::max('id');
        $nextIdEst = $maxIdEst + 1;

        $estPartido = Est_partido::where('partido_id', $partido->id)->firstOrNew();
        $estPartido->id = $nextIdEst;
        $estPartido->partido_id = $partido->id;
        $estPartido->goles_local = trim($goles_local);
        $estPartido->goles_visitante = trim($goles_visitante);
        $estPartido->amarillas = 0;
        $estPartido->rojas = 0;

        $estPartido->save();

        return redirect()->route('admin.partidos.index')->with('success', 'Partido actualizado correctamente.');
    }

    public function delete($id)
    {
        $partido = Partido::findOrFail($id);

        Est_partido::where('partido_id', $partido->id)->delete();

        $partido->delete();

        Session::flash('success', 'Partido eliminado exitosamente.');

        return redirect()->route('admin.partidos.index');
    }

    public function search(Request $request)
    {
        $local = $request->input('equipo_local');
        $visitante = $request->input('equipo_visitante');

        $partido = Partido::where('equipo_local_id', $local)
            ->where('equipo_visitante_id', $visitante)
            ->first();

        if (!$partido) {
            return redirect()->route('admin.partidos.edit')->with('error', 'No se encontró ningún partido con los equipos seleccionados.');
        }
        $equipos = Equipo::all();
        return view('admin.partidos.edit', compact('partido', 'equipos'));
    }

    public function deleteSelected(Request $request)
    {
        $partidosSeleccionados = $request->input('partidosSeleccionados', '');

    if (empty($partidosSeleccionados)) {
        return redirect()->route('admin.partidos.index')->with('success', 'No se han seleccionado partidos para eliminar.');
    }

    // Convertir los IDs de partido a enteros
    $partidosSeleccionados = explode(',', $partidosSeleccionados);
    $partidosSeleccionados = array_map('intval', $partidosSeleccionados);

    // Eliminar las estadísticas de los partidos seleccionados
    Est_partido::whereIn('partido_id', $partidosSeleccionados)->delete();

    // Eliminar los partidos seleccionados
    Partido::whereIn('id', $partidosSeleccionados)->delete();

    return redirect()->back()->with('success', 'Partidos seleccionados eliminados correctamente.');
    }

    public function deleteAll()
    {
        // Obtener todos los partidos
        $partidos = Partido::all();

        // Recorrer todos los partidos y eliminar las estadísticas asociadas
        foreach ($partidos as $partido) {
            Est_partido::where('partido_id', $partido->id)->delete();
            $partido->delete();
        }

        return redirect()->back()->with('success', 'Todos los partidos se han eliminado correctamente.');
    }
}
