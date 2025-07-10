@extends('layout')

@section('content')
<div class="container mt-4">
    <a href="{{ route('calendario.index') }}" class="btn boton-volver-a-calendario">Volver</a>
    @foreach ($partidos as $partido)
    <div class="card mb-3">
        <div class="card-body">
            <h5 class="card-title text-center">Jornada {{ $partido->jornada }}</h5>
            <table class="table table-striped">
                <thead class="thead-dark">
                    <tr>
                        <th scope="col" class="text-center">Fecha</th>
                        <th scope="col" class="text-center">Hora</th>
                        <th scope="col" class="text-center">Partido</th>
                    </tr>
                </thead>
                <tbody>
                    <tr>
                        <td class="text-center">{{ $partido->fecha_nueva }}</td>
                        <td class="text-center">{{ $partido->hora_nueva }}</td>
                        <td class="text-center">
                            <a href="{{ route('partidos', ['id' => $partido->id]) }}" style="text-decoration: none; color: black;">
                                <div class="d-flex justify-content-between align-items-center">
                                    <div class="col text-end me-2">
                                        @php
                                        $nombreLimpioLocal = Str::ascii($partido->equipoLocal->nombre);
                                        $nombreArchivoLocal = strtolower(str_replace(' ', '', $nombreLimpioLocal)) . '.png';
                                        @endphp
                                        <img src="{{ asset('images/equipos/' . $nombreArchivoLocal) }}" alt="{{ $partido->equipoLocal->nombre }}" style="width: 30px; height: auto;" class="me-2">
                                        <strong>{{ $partido->equipoLocal->nombre }}</strong>
                                    </div>
                                    <div class="col-auto text-center">
                                        <span>{{ $partido->resultado }}</span>
                                    </div>
                                    <div class="col text-start ms-2">
                                        <strong>{{ $partido->equipoVisitante->nombre }}</strong>
                                        @php
                                        $nombreLimpioVisitante = Str::ascii($partido->equipoVisitante->nombre);
                                        $nombreArchivoVisitante = strtolower(str_replace(' ', '', $nombreLimpioVisitante)) . '.png';
                                        @endphp
                                        <img src="{{ asset('images/equipos/' . $nombreArchivoVisitante) }}" alt="{{ $partido->equipoVisitante->nombre }}" style="width: 30px; height: auto;" class="me-2">
                                    </div>
                                </div>
                            </a>
                        </td>
                    </tr>
                </tbody>
            </table>
        </div>
    </div>
    @endforeach
</div>
@endsection