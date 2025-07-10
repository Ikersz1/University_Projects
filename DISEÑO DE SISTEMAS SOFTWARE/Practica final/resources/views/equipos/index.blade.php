@extends('layout')

@section('content')
<div class="jumbotron jumbotron-fluid">
    <div class="container-fluid">
        <h1 class="display-4 font-weight-bold text-center" style="font-size: 2rem; color:white;">Equipos de La Liga 2023/24</h1>
    </div>
</div>
@if (session('success'))
<div class="alert alert-success">
    {{ session('success') }}
</div>
@endif

<div class="row justify-content-center mt-4 ml-4 mr-4">
    @foreach ($equipos as $equipo)
    <div class="col-md-4 mb-4">
        <div class="card h-100" style="background-color: #333333; color: #ffffff;">
            <form action="{{ route('equipos.agregarFavorito', $equipo->id) }}" method="POST">
                @auth
                @if (Auth::user()->equipos->contains($equipo->id))
                <form action="{{ route('equipos.eliminarFavorito', $equipo->id) }}" method="POST">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="btn btn-link boton-favorito">
                        <i class="fas fa-heart corazon-lleno"></i>
                    </button>
                </form>
                @else
                <form action="{{ route('equipos.agregarFavorito', $equipo->id) }}" method="POST">
                    @csrf
                    <button type="submit" class="btn btn-link boton-favorito">
                        <i class="far fa-heart corazon-vacio"></i>
                    </button>
                </form>
                @endif
                @endauth
                @csrf
                <div class="card-body d-flex justify-content-center align-items-center">
                    <img src="{{ asset('images/equipos/' . strtolower(str_replace(' ', '', Str::ascii($equipo->nombre))) . '.png') }}" alt="{{ $equipo->nombre }}" style="width: 70px; height: 70px; object-fit: contain; margin-bottom: 15px;">
                    <h5>{{ $equipo->nombre }}</h5>
                </div>
                <div class="list-unstyled text-center">
                    <li>Puntos: {{ $equipo->puntos }}</li>
                    <li>Ganados: {{ $equipo->ganados }}</li>
                    <li>Goles a favor: {{ $equipo->goles_favor }}</li>
                </div>
            </form>
        </div>
    </div>
    @endforeach
</div>
@endsection