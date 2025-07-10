<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>LALIGA EA SPORTS 2023-24</title>
    <link rel="stylesheet" href="https://www.w3schools.com/w3css/4/w3.css">
    <link rel="stylesheet" href="{{ asset('css/styles.css') }}">
    <!-- Google Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Hurricane&family=Roboto+Flex:opsz,wght@8..144,100..1000&display=swap" rel="stylesheet">
    <link href="https://stackpath.bootstrapcdn.com/bootstrap/4.3.1/css/bootstrap.min.css" rel="stylesheet">

    <!-- Estilos para el fondo -->
    <style>
        body {
            background-image: url('{{ asset('public/images/fondo.jpg') }}'); /* Ruta a tu imagen de fondo */
            background-size: cover;
            background-attachment: fixed; /* Fija el fondo */
            background-position: center;
        }
    </style>
</head>

<body>
    <div class="banner">
        <img src="{{ asset('images/tims.png') }}" alt="Banner" usemap="#equiposMap" style="width:100%;">
        <!-- Añade tu imagen de fondo aquí -->
        <map name="equiposMap">
            <!-- Código de tu mapa de imagen -->
        </map>
    </div>

    <div class="w3-sidebar w3-bar-block w3-card w3-animate-left" style="display:none" id="mySidebar">
        <!-- Contenido de tu barra lateral -->
    </div>

    <div id="main">
        <div class="w3-teal">
            <button id="openNav" class="w3-button w3-teal w3-xlarge" onclick="w3_open()">&#9776;</button>
            <div class="title-container w3-container">
                <img src="{{ asset('images/ligaicono.png') }}" alt="Logo La Liga" class="title-logo">
                <h1 class="roboto-flex-title">LALIGA EA SPORTS 2023-24</h1>
            </div>
        </div>
        @yield('content')
    </div>

    <script>
        function w3_open() {
            document.getElementById("main").style.marginLeft = "15%";
            document.getElementById("mySidebar").style.width = "15%";
            document.getElementById("mySidebar").style.display = "block";
            document.getElementById("openNav").style.display = 'none';
        }

        function w3_close() {
            document.getElementById("main").style.marginLeft = "0%";
            document.getElementById("mySidebar").style.display = "none";
            document.getElementById("openNav").style.display = "inline-block";
        }
    </script>

</body>

</html>
