@extends('layout')

@section('title', 'Panel de Administrador')

@section('content')
<!DOCTYPE html>
<html lang="en">

<head>
    <style>

        

/* Media queries for responsiveness */
@media (max-width: 992px) {
    .circle {
        width: 150px;
        height: 180px;
        line-height: 150px;
        font-size: 0.9em;
    }

    .circle a {
        font-size: 1.1em;
    }

    .jumbotron h1 {
        font-size: 2rem;
    }
}

@media (max-width: 768px) {
    .circle {
        width: 130px;
        height: 130px;
        line-height: 130px;
    }

    .circle a {
        font-size: 1em;
    }

    .circle:hover a {
        font-size: 1.2em;
    }

    .jumbotron h1 {
        font-size: 1.8rem;
    }
}

@media (max-width: 576px) {
    .circle-container-wrapper {
        padding: 0 30px; /* Aumentar el espacio en los lados en pantallas pequeñas */
    }

    .circle {
        width: 110px;
        height: 110px;
        line-height: 110px;
    }

    .circle a {
        font-size: 0.9em;
    }

    .circle:hover a {
        font-size: 1.1em;
    }

    .jumbotron h1 {
        font-size: 1.5rem;
    }
}

@media (max-width: 480px) {
    .circle-container-wrapper {
        padding: 0 20px; /* Aumentar el espacio en los lados en pantallas más pequeñas */
    }

    .circle {
        width: 90px;
        height: 90px;
        line-height: 90px;
        font-size: 0.8em;
    }

    .circle a {
        font-size: 0.8em;
    }

    .circle:hover a {
        font-size: 1em;
    }

    .jumbotron h1 {
        font-size: 1.2rem;
    }
}
    </style>
</head>

<body>
    <div class="jumbotron jumbotron-fluid">
        <div class="container-fluid">
            <h1 class="display-4 font-weight-bold text-center text-white">Panel de Administrador</h1>
        </div>
    </div>
    <div class="circle-container-wrapper">
        <div class="circle-container">
            @auth
            @if(Auth::check() && (Auth::user()->role->name === 'noticiero' || Auth::user()->role->name === 'admin'))
            <a href="{{ route('admin.noticias.index') }}" class="circle"><strong>Noticias</strong></a>
            @endif
            @endauth

            @auth
            @if(Auth::check() && (Auth::user()->role->name === 'analista' || Auth::user()->role->name === 'admin'))
            <a href="{{ route('admin.adminjugador') }}" class="circle"><strong>Jugadores</strong></a>
            <a href="{{ route('admin.equipos.index') }}" class="circle"><strong>Equipos</strong></a>
            <a href="{{ route('admin.partidos.index') }}" class="circle"><strong>Partidos</strong></a>
            @endif
            @endauth

            @auth
            @if(Auth::check() && Auth::user()->role->name === 'admin')
            <a href="{{ route('mostrarMensajes') }}" class="circle"><strong>Mensajes</strong></a>
            <a href="{{ route('admin.usuarios.index') }}" class="circle"><strong>Usuarios</strong></a>
            @endif
            @endauth
        </div>
    </div>
</body>

</html>

@endsection
