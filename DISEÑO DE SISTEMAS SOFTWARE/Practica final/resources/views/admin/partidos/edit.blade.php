@extends('layout')

@section('content')
    <div class="container" style="margin-top: 50px; margin-bottom: 50px;">
        <h2>Editar Partido</h2>
        <hr>

        <!-- Mostrar detalles del partido si existe -->
        <h3>Detalles del Partido</h3>
        <form action="{{ route('admin.partidos.update', ['id' => $partido->id]) }}" method="POST">
            @csrf
            @method('PUT')

            <div class="form-group">
                <label for="fecha">Fecha:</label>
                <input type="date" name="fecha" id="fecha" class="form-control" value="{{ $partido->fecha }}">
            </div>
            <div class="form-group">
                <label for="hora">Hora:</label>
                <input type="time" name="hora" id="hora" class="form-control" value="{{ $partido->hora }}">
            </div>
            <div class="form-group">
                <label for="estadio">Estadio:</label>
                <input type="text" name="estadio" id="estadio" class="form-control" value="{{ $partido->estadio }}">
            </div>
            <div class="form-group">
                <label for="resultado">Resultado:</label>
                <input type="text" name="resultado" id="resultado" class="form-control" value="{{ old('resultado', $partido->resultado) }}">
                @error('resultado')
                    <div class="text-danger">{{ $message }}</div>
                @enderror
            </div>
            <div class="form-group">
                <label for="jornada">Jornada:</label>
                <select name="jornada" id="jornada" class="form-control">
                    @for ($i = 1; $i <= 38; $i++)
                        <option value="{{ $i }}" {{ $partido->jornada == $i ? 'selected' : '' }}>{{ $i }}</option>
                    @endfor
                </select>
            </div>
            <div class="form-group">
                <label for="equipo_local">Equipo Local:</label>
                <select name="equipo_local" id="equipo_local" class="form-control">
                    @foreach ($equipos as $equipo)
                        <option value="{{ $equipo->id }}" {{ $partido->equipo_local_id == $equipo->id ? 'selected' : '' }}>
                            {{ $equipo->nombre }}
                        </option>
                    @endforeach
                </select>
            </div>
            <div class="form-group">
                <label for="equipo_visitante">Equipo Visitante:</label>
                <select name="equipo_visitante" id="equipo_visitante" class="form-control">
                    @foreach ($equipos as $equipo)
                        <option value="{{ $equipo->id }}" {{ $partido->equipo_visitante_id == $equipo->id ? 'selected' : '' }}>
                            {{ $equipo->nombre }}
                        </option>
                    @endforeach
                </select>
            </div>
            <div class="d-flex align-items-center mb-3">
                <button type="submit" class="btn btn-primary">Guardar Cambios</button>
                </form>
                <form action="{{ route('admin.partidos.delete', ['id' => $partido->id]) }}" method="POST">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="btn btn-danger" style="margin-left: 0.5rem;" onclick="return confirm('¿Estás seguro de eliminar este partido?')">Eliminar</button>
                </form>
                <div>
                    <a href="{{ route('admin.partidos.index') }}" class="btn btn-secondary" style="margin-left: 53rem;">Cancelar</a>
                </div>
            </div>
    </div>
@endsection
