@extends('layout')

@section('content')
<a href="{{ route('jugadores') }}" class="btn boton-flecha">
    <i class="fa-solid fa-arrow-left-long fa-2xl"></i> <!-- Ícono de flecha -->
</a>
<div class="container mt-5">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card mb-3">
                <div class="row g-0">
                    <div class="col-md-4" style="overflow: hidden;">
                        <img src="{{ Storage::url($jugador->foto) }}" class="img-fluid" alt="Foto de {{ $jugador->nombre }}" style="height: 100%; object-fit: cover; margin-left: 10px;">
                    </div>
                    <div class="col-md-8">
                        <div class="card-body">
                            <h1 class="card-title">{{ $jugador->nombre }}</h1>
                            <p class="card-text"><strong>Equipo:</strong> {{ $jugador->equipo->nombre }}</p>
                            <p class="card-text"><i class="fas fa-futbol"></i> <strong>Posición:</strong> {{ $jugador->posicion }}</p>
                            <p class="card-text"><i class="fas fa-birthday-cake"></i> <strong>Edad:</strong> {{ $jugador->edad }} años</p>
                            <p class="card-text"><strong>Biografía:</strong> {{ $jugador->biografia }}</p>
                        </div>
                    </div>
                </div>
            </div>

            <h2>Estadísticas</h2>
            <div class="row text-center">
                <div class="col-sm-3">
                    <div class="card">
                        <div class="card-body">
                            <h5 class="card-title">{{ $jugador->estadisticas->goles }}</h5>
                            <p class="card-text">Goles</p>
                        </div>
                    </div>
                </div>
                <div class="col-sm-3">
                    <div class="card">
                        <div class="card-body">
                            <h5 class="card-title">{{ $jugador->estadisticas->asistencias }}</h5>
                            <p class="card-text">Asistencias</p>
                        </div>
                    </div>
                </div>
                <div class="col-sm-3">
                    <div class="card">
                        <div class="card-body">
                            <h5 class="card-title">{{ $jugador->estadisticas->amarillas }}</h5>
                            <p class="card-text">Amarillas</p>
                        </div>
                    </div>
                </div>
                <div class="col-sm-3">
                    <div class="card">
                        <div class="card-body">
                            <h5 class="card-title">{{ $jugador->estadisticas->rojas }}</h5>
                            <p class="card-text">Rojas</p>
                        </div>
                    </div>
                </div>
            </div>

            

@endsection
