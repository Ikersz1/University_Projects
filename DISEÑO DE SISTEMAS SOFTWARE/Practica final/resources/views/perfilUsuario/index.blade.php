@extends('layout')

@section('content')
<div class="container">

    <div class="row mb-3 ">
        <div class="col-12 tarjeta-editar-perfil">
            <a href="{{ route('perfilUsuario.edit') }}" class="btn boton-editar-perfil btn-block">Editar Información</a>
        </div>
    </div>

    <div class="row">
        <div class="col-12">
            <div class="tarjeta">
                <div class="encabezado-tarjeta-usuarios">Datos Usuario</div>
                <div class="card-body">
                    <p class="card-text"><strong>Nombre:</strong> {{ $user->name }}</p>
                    <p class="card-text"><strong>Correo:</strong> {{ $user->email }}</p>
                    <!-- Mostrar la foto de perfil -->
                    <p class="card-text"><strong>Foto de perfil:</strong></p>
                    @if($user->profile_picture)
                    <img src="{{ asset($user->profile_picture) }}" alt="Foto de perfil" class="img-fluid perfil-img">
                    @else
                    <img src="{{ asset('images/usuario_rojofinal.png') }}" alt="Foto de perfil" class="img-fluid perfil-img">
                    @endif
                </div>
            </div>
        </div>
    </div>

</div>
@endsection