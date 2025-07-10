@extends('layout')

@section('content')
<div class="container">
    <div class="tarjeta">
        <div class="encabezado-tarjeta-usuarios">¿Seguro que quieres eliminar '{{ $nombreEquipo }}' de tus favoritos?</div>
        <div class="card-body">
            <!-- Formulario para enviar la confirmación de eliminación -->
            <form action="{{ route('favoritos.delete', $nombreEquipo) }}" method="POST">
                @csrf
                @method('DELETE')
                <div class="form-group d-flex justify-content-between">
                    <a href="{{ route('favoritos.index') }}" class="btn boton-cancelar">Cancelar</a>
                    <button type="submit" class="btn btn-danger">Eliminar</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection
