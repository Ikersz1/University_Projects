@extends('layout')

@section('title', 'Mensajes recibidos')

@section('content')

<a href="{{ route('admin.index') }}" class="btn boton-flecha">
    <i class="fa-solid fa-arrow-left-long fa-2xl"></i> <!-- Ícono de flecha -->
</a>

<div class="container mt-4">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="tarjeta">
                <div class="encabezado-tarjeta-usuarios">
                   Mensajes recibidos
                </div>
                <div class="card-body">
                    <ul class="list-group">
                        @forelse ($mensajes as $mensaje)
                            <li class="list-group-item">{{ $mensaje }}</li>
                        @empty
                            <li class="list-group-item">No hay mensajes recibidos</li>
                        @endforelse
                    </ul>
                </div>
                <div class="card-footer">
                    <form action="{{ route('limpiarMensajes') }}" method="POST">
                        @csrf
                        <button type="submit" class="btn boton-limpiar-mensajes">Limpiar mensajes</button>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

