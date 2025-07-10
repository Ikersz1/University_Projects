@extends('layout')

@section('content')


            <div class="card mt-3">
                <div class="card-header bg-dark text-white">
                    <h1 class="mb-0">Editar Jugador</h1>
                </div>
                <div class="card-body">
                    <form action="{{ route('jugadores.actualizar', $jugador->id) }}" method="POST" enctype="multipart/form-data">
                        @csrf
                        @method('PUT')

                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label for="nombre" class="form-label">Nombre:</label>
                                <input type="text" class="form-control" id="nombre" name="nombre" value="{{ $jugador->nombre }}" required>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label for="posicion" class="form-label">Posición:</label>
                                <input type="text" class="form-control" id="posicion" name="posicion" value="{{ $jugador->posicion }}">
                            </div>
                            <div class="col-md-6 mb-3">
                                <label for="nacionalidad" class="form-label">Nacionalidad:</label>
                                <input type="text" class="form-control" id="nacionalidad" name="nacionalidad" value="{{ $jugador->nacionalidad }}">
                            </div>
                            <div class="col-md-6 mb-3">
                                <label for="edad" class="form-label">Edad:</label>
                                <input type="number" class="form-control" id="edad" name="edad" value="{{ $jugador->edad }}">
                            </div>
                            <div class="col-md-6 mb-3">
                                <label for="equipo_id" class="form-label">Equipo:</label>
                                <select class="form-select" id="equipo_id" name="equipo_id">
                                    @foreach ($equipos as $equipo)
                                        <option value="{{ $equipo->id }}" {{ $equipo->id == $jugador->equipo_id ? 'selected' : '' }}>{{ $equipo->nombre }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label for="foto" class="form-label">Foto:</label>
                                <input type="file" class="form-control" id="foto" name="foto">
                                <!-- Mostrar la foto actual -->
                                @if ($jugador->foto)
                                    <div class="mt-2">
                                        <img src="{{ Storage::url($jugador->foto) }}" alt="Foto de {{ $jugador->nombre }}" width="100">
                                    </div>
                                @endif
                            </div>
                        </div>

                        <div class="mb-3">
                            <label for="biografia" class="form-label">Biografía:</label>
                            <textarea class="form-control" id="biografia" name="biografia" rows="4">{{ $jugador->biografia }}</textarea>
                        </div>

                        <h2 class="mb-3">Estadísticas</h2>
                        <div class="row">
                            <div class="col-md-3 mb-3">
                                <label for="goles" class="form-label">Goles:</label>
                                <input type="number" class="form-control" id="goles" name="goles" value="{{ $jugador->estadisticas->goles ?? 0 }}">
                            </div>
                            <div class="col-md-3 mb-3">
                                <label for="asistencias" class="form-label">Asistencias:</label>
                                <input type="number" class="form-control" id="asistencias" name="asistencias" value="{{ $jugador->estadisticas->asistencias ?? 0 }}">
                            </div>
                            <div class="col-md-3 mb-3">
                                <label for="amarillas" class="form-label">Amarillas:</label>
                                <input type="number" class="form-control" id="amarillas" name="amarillas" value="{{ $jugador->estadisticas->amarillas ?? 0 }}">
                            </div>
                            <div class="col-md-3 mb-3">
                                <label for="rojas" class="form-label">Rojas:</label>
                                <input type="number" class="form-control" id="rojas" name="rojas" value="{{ $jugador->estadisticas->rojas ?? 0 }}">
                            </div>
                        </div>

                        <button type="submit" class="btn btn-primary">Actualizar Jugador</button>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
