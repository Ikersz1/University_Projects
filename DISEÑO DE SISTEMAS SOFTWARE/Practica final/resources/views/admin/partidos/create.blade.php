@extends('layout')

@section('content')
    <div class="container">
        <h1 style="text-align: center;">Administración de Partidos</h1>

        <div class="mb-3">
            <h2>Crear Nuevo Partido</h2>
            <form action="{{ isset($partido) ? route('admin.partidos.update', $partido->id) : route('admin.partidos.store') }}" method="POST">
                @csrf
                @if (isset($partido))
                    @method('PUT')
                @endif

                @if(Session::has('success'))
                    <script>
                        window.onload = function() {
                            alert("{{ Session::get('success') }}");
                        };
                    </script>
                @endif

                <div class="mb-3">
                    <label for="fecha" class="form-label">Fecha</label>
                    <input type="date" class="form-control" id="fecha" name="fecha" value="{{ isset($partido) ? $partido->fecha->format('Y-m-d') : '' }}" required>
                </div>
                <div class="mb-3">
                    <label for="hora" class="form-label">Hora</label>
                    <input type="time" class="form-control" id="hora" name="hora" value="{{ isset($partido) ? $partido->fecha->format('H:i') : '' }}" required>
                </div>
                <div class="mb-3">
                    <label for="estadio" class="form-label">Estadio</label>
                    <input type="text" class="form-control" id="estadio" name="estadio" value="{{ isset($partido) ? $partido->estadio : '' }}" required>
                </div>
                <div class="mb-3">
                    <label for="equipo_local" class="form-label">Equipo Local</label>
                    <select class="form-control" id="equipo_local" name="equipo_local" required>
                        <option value="">Selecciona un equipo local</option>
                        @foreach ($equipos as $equipo)
                            <option value="{{ $equipo->id }}" {{ isset($partido) && $partido->equipo_local == $equipo->id ? 'selected' : '' }}>{{ $equipo->nombre }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="mb-3">
                    <label for="resultado" class="form-label">Resultado</label>
                    <input type="text" class="form-control" id="resultado" name="resultado" value="{{ isset($partido) ? $partido->resultado : '' }}">
                </div>
                <div class="mb-3">
                    <label for="equipo_visitante" class="form-label">Equipo Visitante</label>
                    <select class="form-control" id="equipo_visitante" name="equipo_visitante" required>
                        <option value="">Selecciona un equipo visitante</option>
                        @foreach ($equipos as $equipo)
                            <option value="{{ $equipo->id }}" {{ isset($partido) && $partido->equipo_visitante == $equipo->id ? 'selected' : '' }}>{{ $equipo->nombre }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="mb-3">
                    <label for="jornada" class="form-label">Jornada</label>
                    <select class="form-control" id="jornada" name="jornada" required>
                        <option value="">Selecciona la jornada</option>
                        @for ($i = 1; $i <= 38; $i++)
                            <option value="{{ $i }}" {{ isset($partido) && $partido->jornada == $i ? 'selected' : '' }}>Jornada {{ $i }}</option>
                        @endfor
                    </select>
                </div>

                <button type="submit" class="btn btn-primary">Crear Partido</button>
                <a href="{{ route('admin.partidos.index') }}" class="btn btn-secondary">Volver</a>
            </form>
        </div>
@endsection