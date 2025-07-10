@extends('layout')

@section('title', 'La Liga')

@section('content')

<div class="container">
    <div class="jumbotron jumbotron-fluid">
        <div class="container-fluid">
            <h1 class="display-4 font-weight-bold text-center" style="font-size: 2rem; color:white;">{{ __('home.home') }}</h1>
        </div>
    </div>

    <div class="row">
        <div class="col-md-12">
            <!-- Carrusel de noticias que ocupa todo el ancho de la página -->
            <div id="noticias-carousel" class="slick-carousel-large">
                @foreach ($noticias->take(3) as $noticia)
                <div class="card">
                    <img src="{{ $noticia->enlace_de_la_foto }}" class="card-img-top noticia-img" alt="{{ $noticia->titulo }}">
                    <div class="card-body">
                        <h5 class="card-title">{{ $noticia->titulo }}</h5>
                    </div>
                </div>
                @endforeach
            </div>
        </div>
    </div>

    <div class="row mt-4">
        <div class="col-md-12 text-center">
            <h2 class="mt-4 font-weight-bold">{{ __('home.three_best') }}</h2>
        </div>
    </div>

    <div class="row mt-4 mb-4">
        <div class="col-md-12 text-center">
            <!-- Mostrar los tres primeros equipos de la liga con sus escudos y puntos -->
            <div class="d-flex justify-content-between">
                @foreach ($equipos->take(3) as $index => $equipo)
                <div class="col-md-4 mb-3 text-center">
                    @php
                    // Construye la ruta de la imagen del escudo
                    $nombreLimpio = Str::ascii($equipo->nombre);
                    $nombreArchivo = strtolower(str_replace(' ', '', $nombreLimpio)) . '.png';
                    $rutaImagen = asset('images/equipos/' . $nombreArchivo);
                    // Verifica si la imagen del escudo existe
                    $imagenExiste = file_exists(public_path('images/equipos/' . $nombreArchivo));
                    @endphp
                    @if($imagenExiste)
                    <img src="{{ $rutaImagen }}" alt="{{ $equipo->nombre }}" class="img-fluid">
                    @else
                    <p>No se encontró el escudo del equipo</p>
                    @endif
                    <p class="font-weight-bold">{{ $equipo->nombre }}</p>
                    <p>{{ __('home.points') }}: {{ $equipo->puntos }}</p>
                </div>
                @endforeach
            </div>
        </div>
    </div>

    <div class="row mt-4">
    <div class="col-md-12 text-center">
        <h2 class="mt-4 mb-4 font-weight-bold">{{ __('home.next_matches') }}</h2>
        <!-- Centrar tanto el texto como el carrusel -->
        <div class="d-flex justify-content-center align-items-center">
            <div id="partidos-carousel" class="slick-carousel-small" style="max-width:600px;">
                @foreach ($partidos as $partido)
                    @php
                    $nombreLimpioLocal = Str::ascii($partido->equipoLocal->nombre);
                    $nombreArchivoLocal = strtolower(str_replace(' ', '', $nombreLimpioLocal)) . '.png';

                    $nombreLimpioVisitante = Str::ascii($partido->equipoVisitante->nombre);
                    $nombreArchivoVisitante = strtolower(str_replace(' ', '', $nombreLimpioVisitante)) . '.png';
                    @endphp
                    <!-- Quitar la clase card-body para eliminar el fondo blanco -->
                    <div class="card mb-3" style="background-color: transparent;">
                        <div class="d-flex justify-content-between align-items-center">
                            <div class="d-flex align-items-center">
                                <img src="{{ asset('images/equipos/' . $nombreArchivoLocal) }}" alt="{{ $partido->equipoLocal->nombre }}" style="width: 125px; height: auto;">
                                <span class="ml-3">{{ $partido->equipoLocal->nombre }}</span>
                            </div>
                            <span>{{ $partido->fecha }} - {{ $partido->hora }}</span>
                            <div class="d-flex align-items-center">
                                <span>{{ $partido->equipoVisitante->nombre }}</span>
                                <img src="{{ asset('images/equipos/' . $nombreArchivoVisitante) }}" alt="{{ $partido->equipoVisitante->nombre }}" style="width: 125px; height: auto;">
                            </div>
                        </div>
                    </div>
                    @endforeach
                </div>
            </div>
        </div>
    </div>


    <!-- Incluye los estilos y scripts de Slick Carousel -->
    <link rel="stylesheet" type="text/css" href="https://cdnjs.cloudflare.com/ajax/libs/slick-carousel/1.8.1/slick.min.css" />
    <link rel="stylesheet" type="text/css" href="https://cdnjs.cloudflare.com/ajax/libs/slick-carousel/1.8.1/slick-theme.min.css" />
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.6.0/jquery.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/slick-carousel/1.8.1/slick.min.js"></script>

    <script>
        $(document).ready(function() {
            $('#noticias-carousel').slick({
                infinite: true,
                slidesToShow: 1,
                slidesToScroll: 1,
                autoplay: true,
                autoplaySpeed: 2000,
                arrows: true,
            });

            $('#partidos-carousel').slick({
                infinite: true,
                slidesToShow: 1, // Mostrar solo un partido a la vez
                slidesToScroll: 1, // Desplazarse de a un partido a la vez
                autoplay: true,
                autoplaySpeed: 2000,
                arrows: true,
                responsive: [{
                    breakpoint: 768, // Ajustar en pantallas más pequeñas
                    settings: {
                        slidesToShow: 1,
                        slidesToScroll: 1
                    }
                }]
            });

            $('#partidos-carousel').addClass('slick-carousel-small'); // Agregar clase para carrusel más pequeño
        });
    </script>

    <style>
        .slick-carousel-small .slick-prev,
        .slick-carousel-small .slick-next {
            top: 30%;
        }

        .slick-carousel-large .slick-prev,
        .slick-carousel-large .slick-next {
            top: 40%;
        }

        .card-title {
            position: absolute;
            top: 80%;
            left: 55%;
            transform: translate(-50%, -50%);
            color: white;
            font-weight: bold;
            text-shadow: 2px 2px 4px rgba(0, 0, 0, 0.7);
            font-size: 50px;
            width: 900px;
            background: linear-gradient(to right, transparent, rgba(0, 0, 0, 0.5));
            padding: 10px;
        }

    

    @media (max-width: 992px) {
        .card-title {
            font-size: 40px;
            width: 700px;
        }
    }

    @media (max-width: 768px) {
        .card-title {
            font-size: 30px;
            width: 500px;
        }
    }

    @media (max-width: 576px) {
        .card-title {
            font-size: 20px;
            width: 300px;
        }
    }

    @media (max-width: 480px) {
        .card-title {
            font-size: 16px;
            width: 250px;
        }
    }
    
    #partidos-carousel .card {
        width: 300px; 
    }
    
    .card{
    	border: none;
    }
    	
    .slick-prev:before,
    .slick-next:before {
		color: black; /* Cambia el color de las flechas a negro */
    }
</style>

<a href="{{ url('lang/en') }}">English</a> | <a href="{{ url('lang/es') }}">Español</a>

@endsection
