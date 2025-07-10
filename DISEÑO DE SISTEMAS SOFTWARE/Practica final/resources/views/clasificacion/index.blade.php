@extends('layout')

@section('title', 'Tabla de Clasificación de La Liga 2023/24')

@section('content')
<div class="jumbotron jumbotron-fluid">
    <div class="container-fluid">
        <!-- Eliminé el contenedor para el título y lo coloqué directamente en el jumbotron -->
        <h1 class="display-4 font-weight-bold" style="font-size: 2.5rem; color:white; text-align: center">Tabla de Clasificación de La Liga 2023/24</h1>
        <!-- Agrega botones para ordenar la tabla -->
        <div class="mt-5 text-center">
            <a href="{{ route('clasificacion', ['order' => 'puntos']) }}" class="btn btn-primary {{ isset($order) && $order == 'puntos' ? 'active' : '' }}" style="background-color: {{ isset($order) && $order == 'puntos' ? '#ff0000' : '#ffffff' }}; color: {{ isset($order) && $order == 'puntos' ? '#ffffff' : '#000000' }}; margin-right: 5px; border-color: {{ isset($order) && $order == 'puntos' ? '#ffffff' : '#ffffff' }};">Puntos</a>
            <a href="{{ route('clasificacion', ['order' => 'goles_favor']) }}" class="btn btn-primary {{ $order == 'goles_favor' ? 'active' : '' }}" style="background-color: {{ $order == 'goles_favor' ? '#ff0000' : '#ffffff' }}; color: {{ $order == 'goles_favor' ? '#ffffff' : '#000000' }}; margin-right: 5px; border-color: {{ $order == 'goles_favor' ? '#ffffff' : '#ffffff' }};">Goles a Favor</a>
            <a href="{{ route('clasificacion', ['order' => 'goles_contra']) }}" class="btn btn-primary {{ $order == 'goles_contra' ? 'active' : '' }}" style="background-color: {{ $order == 'goles_contra' ? '#ff0000' : '#ffffff' }}; color: {{ $order == 'goles_contra' ? '#ffffff' : '#000000' }}; border-color: {{ $order == 'goles_contra' ? '#ffffff' : '#ffffff' }};">Goles en Contra</a>
        </div>
    </div>
</div>
<div class="container">
    <div class="table-responsive">
        <table class="table table-striped table-bordered table-hover" style="width: 100%; cursor: pointer;">
            <thead class="thead-dark">
                <tr>
                    <th class="text-center">Pos</th>
                    <th class="text-center">Equipo</th>
                    <th class="text-center">PJ</th>
                    <th class="text-center">PG</th>
                    <th class="text-center">PE</th>
                    <th class="text-center">PP</th>
                    <th class="text-center">GF</th>
                    <th class="text-center">GC</th>
                    <th class="text-center">Dif</th>
                    <th class="text-center">Pts</th>
                    <th class="text-center">Estado</th>
                </tr>
            </thead>
            <tbody>
                @foreach($equipos as $equipo)
                <tr style="transition: background-color 0.3s;">
                    <td class="text-center">{{ $equipo->posicion_original }}</td>
                    <td class="text-center">{{ $equipo->nombre }}</td>
                    <td class="text-center">{{ $equipo->partidos_jugados }}</td>
                    <td class="text-center">{{ $equipo->ganados }}</td>
                    <td class="text-center">{{ $equipo->empatados }}</td>
                    <td class="text-center">{{ $equipo->perdidos }}</td>
                    <td class="text-center">{{ $equipo->goles_favor }}</td>
                    <td class="text-center">{{ $equipo->goles_contra }}</td>
                    <td class="text-center">{{ $equipo->goles_favor - $equipo->goles_contra }}</td>
                    <td class="text-center">{{ $equipo->puntos }}</td>
                    <td class="text-center">
                        @if($equipo->posicion_original <= 4) <span class="badge badge-primary">Champions</span>
                            @elseif($equipo->posicion_original == 5)
                            <span class="badge badge-warning">Europa League</span>
                            @elseif($equipo->posicion_original == 6)
                            <span class="badge badge-success">Conference League</span>
                            @elseif($equipo->posicion_original > count($equipos) - 3)
                            <span class="badge badge-danger">Descendido</span>
                            @endif
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>
</div>


@endsection