@extends('layout')

@section('content')
<div class="container mt-4">
    <a style="margin-bottom: 1rem;" href="{{ route('admin.partidos.index') }}" class="btn btn-secondary">Volver</a>
    @foreach ($partidos as $partido)
    <div class="card mb-3">
        <div class="card-body">
            <h5 class="card-title text-center">Jornada {{ $partido->jornada }}</h5>
            <table class="table table-striped">
                <thead class="thead-dark">
                    <tr>
                        <th scope="col" class="text-center">Fecha</th>
                        <th scope="col" class="text-center">Hora</th>
                        <th scope="col" class="text-center">Partido</th>
                        <th scope="col" class="text-center">Acciones</th>
                    </tr>
                </thead>
                <tbody>
                    <tr>
                        <td class="text-center">{{ $partido->fecha_nueva }}</td>
                        <td class="text-center">{{ $partido->hora_nueva }}</td>
                        <td class="text-center">
                            <div class="d-flex justify-content-between align-items-center">
                                <div class="col text-end me-2">
                                    <img src="{{ asset('images/equipos/' . strtolower(str_replace(' ', '', Str::ascii($partido->equipoLocal->nombre))) . '.png') }}" alt="{{ $partido->equipoLocal->nombre }}" style="width: 30px; height: auto;" class="me-2">
                                    <strong>{{ $partido->equipoLocal->nombre }}</strong>
                                </div>
                                <div class="col-auto text-center">
                                    <span>{{ $partido->resultado }}</span>
                                </div>
                                <div class="col text-start ms-2">
                                    <strong>{{ $partido->equipoVisitante->nombre }}</strong>
                                    <img src="{{ asset('images/equipos/' . strtolower(str_replace(' ', '', Str::ascii($partido->equipoVisitante->nombre))) . '.png') }}" alt="{{ $partido->equipoVisitante->nombre }}" style="width: 30px; height: auto;" class="me-2">
                                </div>
                            </div>
                        </td>
                        <td class="text-center">
                        <a href="{{ route('admin.partidos.edit', ['id' => $partido->id]) }}" class="btn">
                            <i class="fas fa-pencil-alt"></i>
                        </a>
                        <form action="{{ route('admin.partidos.delete', ['id' => $partido->id]) }}" method="POST" style="display: inline-block;">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="btn btn-link text-danger btn-eliminar" onclick="return confirm('¿Estás seguro de eliminar este partido?')">
                                <i class="fas fa-trash-alt"></i>
                            </button>
                        </form>
                    </td>
                    </tr>
                </tbody>
            </table>
        </div>
    </div>
    @endforeach
</div>
@endsection