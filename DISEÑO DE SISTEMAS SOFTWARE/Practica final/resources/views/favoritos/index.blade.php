@extends('layout')

@section('content')
<div class="container">

    @if (isset($equiposFavoritos) && $equiposFavoritos->isNotEmpty())
    <h1 class="texto-h1-favoritos">Mis Equipos Favoritos</h1>
    <div class="row">
        @foreach ($equiposFavoritos as $equipo)
        <div class="col-md-4 mb-4 tarjeta-contenedor">
            <div class="tarjeta-favoritos">
                <!-- Botón del corazón -->
                <form action="{{ route('favoritos.edit', $equipo->nombre) }}" method="GET">
                    @csrf
                    <button type="submit" class="btn btn-link boton-favorito">
                        <i class="fas fa-heart corazon-lleno" ;"></i>
                    </button>
                </form>
                <div class="text-center"> <!-- Contenedor para centrar la imagen -->
                    @php
                    $nombreLimpioLocal = Str::ascii($equipo->nombre);
                    $nombreArchivoLocal = strtolower(str_replace(' ', '', $nombreLimpioLocal)) . '.png';
                    @endphp
                    <img src="{{ asset('images/equipos/' . $nombreArchivoLocal) }}" alt="{{ $equipo->nombre }}" style="width: 70px; height: 70px; object-fit: contain; margin-right: 20px;">
                </div>
                <!-- Contenido del equipo -->
                <div class="cuerpo-tarjetas-favoritos">
                    <h5 class="tarjeta-favoritos-nombreEquipo">{{ $equipo->nombre }}</h5>
                    <ul class="list-group list-group-flush" style="align-items: center">
                        <li class="tarjetas-fav list-group-item">Puntos: {{ $equipo->puntos }}</li>
                        <li class="tarjetas-fav list-group-item">Partidos jugados: {{ $equipo->partidos_jugados }}</li>
                        <li class="tarjetas-fav list-group-item">Goles a favor: {{ $equipo->goles_favor }}</li>
                        <li class="tarjetas-fav list-group-item">Goles en contra: {{ $equipo->goles_contra }}</li>
                    </ul>
                </div>
            </div>
        </div>
        @endforeach
    </div>
    @elseif (isset($warning))
    <div class="alert alert-warning text-center" role="alert">
        {{ $warning }}
    </div>
    @endif
</div>
@endsection
