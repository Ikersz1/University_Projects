@extends('layout')

@section('title', 'Calendario de Partidos')

@section('content')
<div class="jumbotron jumbotron-fluid">
    <div class="container-fluid">
        <h1 class="display-4 font-weight-bold text-center" style="font-size: 2rem; color:white;">Calendario de La Liga 2023/24</h1>
    </div>
</div>

<div class="container mt-4">
    <div class="row" style="margin-bottom: 1rem;">
        <div class="col-md-6">
            <select class="boton-seleccionar-equipo dropdown-toggle" onchange="location = this.value;">
                <option value="" selected disabled>Selecciona un equipo</option>
                @foreach ($equipos as $equipo)
                <option value="{{ route('calendario.show', ['equipo' => $equipo->id]) }}">{{ $equipo->nombre }}</option>
                @endforeach
            </select>
        </div>
    </div>
    <div class="d-flex justify-content-between align-items-center mb-3">
        <a href="{{ route('calendario.index', ['jornada' => $jornada_actual - 1]) }}" class="jornada-anterior">
            <i class="fas fa-arrow-left"></i>
        </a>
        <select onchange="location = this.value;" class="jornada-actual">
            @foreach($jornadas as $jornada)
            <option value="{{ route('calendario.index', ['jornada' => $jornada]) }}" @if($jornada==$jornada_actual) selected @endif>
                Jornada {{ $jornada }}
            </option>
            @endforeach
        </select>
        <a href="{{ route('calendario.index', ['jornada' => $jornada_actual + 1]) }}" class="jornada-siguiente">
            <i class="fas fa-arrow-right"></i>
        </a>
    </div>

    <div class="table-responsive">
        <table class="table table-striped">
            <thead class="thead-dark">
                <tr>
                    <th scope="col" class="text-center">Fecha</th>
                    <th scope="col" class="text-center">Hora</th>
                    <th scope="col" class="text-center">Partido</th>
                </tr>
            </thead>
            <tbody>
                <div>
                    @foreach ($partidos as $index => $partido)
                    @php
                    $claseFila = $index % 2 == 0 ? 'fila-par' : 'fila-impar';
                    @endphp
                    <tr class="{{ $claseFila }}" data-partido-id="{{ $partido->id }}">
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
                    @endforeach
                </div>
            </tbody>
        </table>
    </div>
    @endsection

    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script>
        $(document).ready(function() {
            $('.ver-estadisticas').on('click', function() {
                var partidoId = $(this).closest('.partido-row').data('partido-id');
                var statsRow = $(this).closest('.partido-row').next('.stats-row');

                if (statsRow.is(':visible')) {
                    statsRow.hide();
                } else {
                    // Aquí deberías realizar una petición AJAX para obtener las estadísticas del partido
                    // y luego actualizar el contenido de .estadisticas-container
                    // Por ejemplo:
                    $.ajax({
                        method: 'GET',
                        url: '/obtener-estadisticas/' + partidoId,
                        success: function(data) {
                            $('#estadisticas-' + partidoId).html(data);
                            statsRow.show();
                        },
                        error: function(xhr, status, error) {
                            console.error(error);
                        }
                    });
                }
            });
        });
    </script>