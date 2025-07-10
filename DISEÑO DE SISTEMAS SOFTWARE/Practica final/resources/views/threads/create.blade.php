@extends('layout')

@section('content')

<a href="{{ route('threads.index') }}" class="btn boton-flecha">
    <i class="fa-solid fa-arrow-left-long fa-2xl"></i> <!-- Ícono de flecha -->
</a>

<div class="container mt-4">
    <div class=" mb-5">
        <h1 style="text-align: center;">Crear un Nuevo Hilo</h1>
    </div>

    <div class="row justify-content-center">
        <div class="col-md-8">
            <form action="{{ route('threads.store') }}" method="POST" enctype="multipart/form-data">
                @csrf
                <div class="mb-3" style="align-items: center;">
                    <label for="topic" class="form-label">Tema</label>
                    <input type="text" class="form-control" id="topic" name="topic" required>
                </div>
                <div class="mb-3">
                    <label for="content" class="form-label">Contenido</label>
                    <textarea class="form-control" id="content" name="content" rows="5" required></textarea>
                </div>
                <div class="d-flex justify-content-end">
                    <button type="submit" class="btn boton-crear-hilo">Crear Hilo</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection