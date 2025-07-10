@extends('layout')

@section('content')
<div class="container mt-5">
    <div class="row justify-content-center">
        <div class="col-md-6">
            <div class="tarjeta">
                <div class="encabezado-tarjeta-usuarios">¿Desea cerrar sesión?</div>

                <div class="card-body">
                    <p>Hasta la próxima, {{ Session::get('userName') }}</p>
                    <form method="POST" action="{{ route('logout') }}">
                        @csrf
                        <div class="form-group d-flex justify-content-between">
                            <a href="{{ route('home') }}" class="btn boton-cancelar">Cancelar</a>
                            <button type="submit" class="btn boton-inicio">Confirmar</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
