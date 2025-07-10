@extends('layout')

@section('content')
<div class="jumbotron jumbotron-fluid" style="background-color: #333333; color: #ffffff;">
    <div class="container-fluid">
        <div class="display-4 font-weight-bold text-center">
            <img src="{{ asset('images/equipos/' . strtolower(str_replace(' ', '', Str::ascii($partido->equipoLocal->nombre))) . '.png') }}" alt="{{ $partido->equipoLocal->nombre }}" style="width: 100px; height: auto;" class="me-2">
            <strong style="font-size: 30px;">{{ $partido->equipoLocal->nombre }}</strong>

            <span class="mx-3" style="font-size: 24px;">{{ $partido->resultado }}</span>

            <strong style="font-size: 24px;">{{ $partido->equipoVisitante->nombre }}</strong>
            <img src="{{ asset('images/equipos/' . strtolower(str_replace(' ', '', Str::ascii($partido->equipoVisitante->nombre))) . '.png') }}" alt="{{ $partido->equipoVisitante->nombre }}" style="width: 100px; height: auto;" class="ms-2">

            <p style="font-size: 18px;">{{ $partido->fecha_nueva }} {{ $partido->hora_nueva }}</p>
            <p style="font-size: 18px;"> {{ $partido->estadio }}</p>
            @if ($partido->resultado == ' - ')
            <p style="font-size: 18px;">Estado: Sin Empezar</p>
            @else
            <p style="font-size: 18px;">Estado: Finalizado</p>
            @endif
        </div>

        @if (Auth::check())
        @if ($partido->resultado == ' - ')
        @if (!$haVotado)
        <!-- Mostrar formulario de predicción si el usuario no ha votado -->
        <div class="container">
            <div class="card-header text-center" style="background-color: #646464;">
                <strong style="font-size: 20px;">¿Quién ganará?</strong>
                <hr>
                <div class="prediccion">
                    <div>
                        <!-- Botón para votar por equipo local -->
                        <button class="circulo-equipo" onclick="votar('local')">
                            <img src="{{ asset('images/equipos/' . strtolower(str_replace(' ', '', Str::ascii($partido->equipoLocal->nombre))) . '.png') }}" alt="{{ $partido->equipoLocal->nombre }}" style="width: 40px; height: auto;">
                        </button>
                        <strong style="font-size: 14px;">{{ $partido->equipoLocal->nombre }}</strong>
                    </div>
                    <div>
                        <!-- Botón para votar por empate -->
                        <button class="circulo-equipo" onclick="votar('empate')">
                            X
                        </button>
                        <strong style="font-size: 14px;">Empate</strong>
                    </div>
                    <div>
                        <!-- Botón para votar por equipo visitante -->
                        <button class="circulo-equipo" onclick="votar('visitante')">
                            <img src="{{ asset('images/equipos/' . strtolower(str_replace(' ', '', Str::ascii($partido->equipoVisitante->nombre))) . '.png') }}" alt="{{ $partido->equipoVisitante->nombre }}" style="width: 40px; height: auto;">
                        </button>
                        <strong style="font-size: 14px;">{{ $partido->equipoVisitante->nombre }}</strong>
                    </div>
                </div>
            </div>
        </div>
        @endif
        @if ($haVotado)
        <!-- Mostrar porcentajes de votos si el usuario ha votado -->
        <div class="container">
            <div class="card-header text-center" style="background-color: #646464;">
                <strong style="font-size: 20px;">Porcentajes de Votos</strong>
                <hr style="color: white;">
                <div class="prediccion-resultados">
                    <div class="prediccion-item">
                        <div class="equipo-info">
                            <img src="{{ asset('images/equipos/' . strtolower(str_replace(' ', '', Str::ascii($partido->equipoLocal->nombre))) . '.png') }}" alt="{{ $partido->equipoLocal->nombre }}" style="width: 40px; height: auto;">
                            <strong>{{ $partido->equipoLocal->nombre }}</strong>
                        </div>
                        <div class="barra-progreso">
                            <div class="relleno" style="width: {{ $porcentajeLocalFormatted }}%;"></div>
                        </div class="porcentajeFinal">
                        <strong>{{ $porcentajeLocalFormatted }}%</strong>
                    </div>

                    <div class="prediccion-item">
                        <div class="equipo-info">
                            <strong style="font-size: 17px;">Empate</strong>
                        </div>
                        <div class="barra-progreso">
                            <div class="relleno" style="width: {{ $porcentajeEmpateFormatted }}%;"></div>
                        </div>
                        <strong>{{ $porcentajeEmpateFormatted }}%</strong>
                    </div>

                    <div class="prediccion-item">
                        <div class="equipo-info">
                            <img src="{{ asset('images/equipos/' . strtolower(str_replace(' ', '', Str::ascii($partido->equipoVisitante->nombre))) . '.png') }}" alt="{{ $partido->equipoVisitante->nombre }}" style="width: 40px; height: auto;">
                            <strong>{{ $partido->equipoVisitante->nombre }}</strong>
                        </div>
                        <div class="barra-progreso">
                            <div class="relleno" style="width: {{ $porcentajeVisitanteFormatted }}%;"></div>
                        </div>
                        <strong>{{ $porcentajeVisitanteFormatted }}%</strong>
                    </div>
                </div>
            </div>
        </div>
        @endif
        @endif
        @endif
    </div>
</div>
@if ($partido->resultado != ' - ')
<div class="container">
    <div class="row">
        <div class="col-md-6">
            <div class="card">
                <div class="card-header text-center">
                    <img src="{{ asset('images/equipos/' . strtolower(str_replace(' ', '', Str::ascii($partido->equipoLocal->nombre))) . '.png') }}" alt="{{ $partido->equipoLocal->nombre }}" style="width: 50px; height: auto;" class="me-2">
                    <strong>{{ $partido->equipoLocal->nombre }}</strong>
                </div>
                <div class="card-body">
                    <p>Goles: {{ $estPartido->goles_local }}</p>
                    <p>Amarillas: {{ $estPartido->amarillas }}</p>
                    <p>Rojas: {{ $estPartido->rojas }}</p>
                </div>
            </div>
        </div>

        <div class="col-md-6">
            <div class="card">
                <div class="card-header text-center">
                    <img src="{{ asset('images/equipos/' . strtolower(str_replace(' ', '', Str::ascii($partido->equipoVisitante->nombre))) . '.png') }}" alt="{{ $partido->equipoVisitante->nombre }}" style="width: 50px; height: auto;" class="me-2">
                    <strong>{{ $partido->equipoVisitante->nombre }}</strong>
                </div>
                <div class="card-body">
                    <p>Goles: {{ $estPartido->goles_visitante }}</p>
                    <p>Amarillas: {{ $estPartido->amarillas }}</p>
                    <p>Rojas: {{ $estPartido->rojas }}</p>
                </div>
            </div>
        </div>
    </div>
</div>
@endif

<script>
    function votar(opcion) {
        fetch('{{ route("guardarVoto") }}', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': '{{ csrf_token() }}'
                },
                body: JSON.stringify({
                    partido_id: '{{ $partido->id }}',
                    opcion: opcion
                })
            })
            .then(response => response.json())
            .then(data => {
                location.reload();
            })
            .catch(error => {
                console.error('Error al votar:', error);
            });
    }
</script>
@endsection