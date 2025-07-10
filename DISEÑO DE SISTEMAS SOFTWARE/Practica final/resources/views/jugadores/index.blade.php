@extends('layout')

@section('content')
<div class="jumbotron jumbotron-fluid">
    <div class="container-fluid">
        <h1 class="display-4 font-weight-bold text-center" style="font-size: 2.5rem; color:white;">Jugadores de La Liga 2023/24</h1>
    </div>
</div>
<script src="https://code.jquery.com/jquery-3.3.1.slim.min.js"></script>

<!-- Popper.js (Necesario para los dropdowns, tooltips, y popovers en Bootstrap 4) -->
<script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.14.7/umd/popper.min.js"></script>

<!-- Bootstrap JS -->
<script src="https://stackpath.bootstrapcdn.com/bootstrap/4.3.1/js/bootstrap.min.js"></script>
<script src="https://stackpath.bootstrapcdn.com/bootstrap/5.0.0-alpha1/js/bootstrap.bundle.min.js"></script>
<div class="container">

    <!-- Botones desplegables para ordenar -->
    
    <div class="dropdown mb-3">
        <button class="btn dropdown-toggle " type="button" id="dropdownMenuButton" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
            Ordenar por
        </button>
        <div class="dropdown-menu" aria-labelledby="dropdownMenuButton">
            <a class="dropdown-item" href="#" onclick="orderBy('goles', 'desc')">Goles (Mayor a Menor)</a>
            <a class="dropdown-item" href="#" onclick="orderBy('goles', 'asc')">Goles (Menor a Mayor)</a>
            <a class="dropdown-item" href="#" onclick="orderBy('asistencias', 'desc')">Asistencias (Mayor a Menor)</a>
            <a class="dropdown-item" href="#" onclick="orderBy('asistencias', 'asc')">Asistencias (Menor a Mayor)</a>
            <a class="dropdown-item" href="#" onclick="orderBy('amarillas', 'desc')">Amarillas (Mayor a Menor)</a>
            <a class="dropdown-item" href="#" onclick="orderBy('amarillas', 'asc')">Amarillas (Menor a Mayor)</a>
            <a class="dropdown-item" href="#" onclick="orderBy('rojas', 'desc')">Rojas (Mayor a Menor)</a>
            <a class="dropdown-item" href="#" onclick="orderBy('rojas', 'asc')">Rojas (Menor a Mayor)</a>
        </div>
    </div>
    
<input type="text" id="search" placeholder="Buscar jugadores..." onkeyup="fetchData()" class="form-control mb-3">
    <div class="table-responsive">
        <table class="table table-striped table-bordered" style="width: 100%; margin: auto;">
            <thead class="thead-dark">
                <tr>
                    <th>Nombre</th>
                    <th>Posición</th>
                    <th>Edad</th>
                    <th>Goles</th>
                    <th>Asistencias</th>
                    <th>Amarillas</th>
                    <th>Rojas</th>
                    <th>Equipo</th>
                </tr>
            </thead>
            <tbody id="jugadores-list">
                <!-- Asumiendo que vas a llenar esto dinámicamente con fetchData() -->
            </tbody>
        </table>
        <div id="pagination-links" class="d-flex justify-content-center">
    <!-- Los enlaces de paginación se cargarán aquí -->
</div>
    </div>
</div>


<script>
var orderField = 'nombre'; // Ordenar por nombre por defecto
var orderDirection = 'asc'; // Dirección ascendente por defecto

document.addEventListener('DOMContentLoaded', function() {
    fetchData(); // Cargar todos los jugadores inicialmente
});

function orderBy(field, direction) {
    orderField = field;
    orderDirection = direction;
    fetchData();
}
var selectedTeamId = ''; // Mantener el equipo seleccionado

function filterByTeam(teamId) {
    selectedTeamId = teamId;
    fetchData();
}
function fetchData(page = 1) {
    var search = document.getElementById('search').value;
    var url = `/jugadores?search=${search}&orderField=${orderField}&orderDirection=${orderDirection}&page=${page}`;
    console.log("Fetching data from URL:", url); // Verifica la URL solicitada

    fetch(url, {
        method: 'GET',
        headers: {
            'X-Requested-With': 'XMLHttpRequest',
        },
    })
    .then(response => response.json())
    .then(data => {
        updateTable(data); // Asegúrate de que esta función maneja correctamente los datos y los enlaces
        attachClickEventToPaginationLinks(); // Re-attach click events to new pagination links
    })
    .catch(error => console.error('Error:', error));
}



function updateTable(data) {
    var tableBody = document.getElementById('jugadores-list');
    var paginationDiv = document.getElementById('pagination-links');

    tableBody.innerHTML = ''; // Limpiar la tabla
    data.data.forEach(jugador => {
        var goles = jugador.estadisticas ? jugador.estadisticas.goles : 'N/A';
        var asistencias = jugador.estadisticas ? jugador.estadisticas.asistencias : 'N/A';
        var amarillas = jugador.estadisticas ? jugador.estadisticas.amarillas : 'N/A';
        var rojas = jugador.estadisticas ? jugador.estadisticas.rojas : 'N/A';
        var row = `<tr>
            <td><a style='color: red;' href="/jugadores/${jugador.id}">${jugador.nombre}</a></td>
            <td>${jugador.posicion}</td>
            <td>${jugador.edad}</td>
            <td>${goles}</td>
            <td>${asistencias}</td>
            <td>${amarillas}</td>
            <td>${rojas}</td>
            <td>${jugador.equipo ? jugador.equipo.nombre : 'N/A'}</td>
        </tr>`;
        tableBody.innerHTML += row;
    });
    paginationDiv.innerHTML = data.links; // Actualizar los enlaces de paginación
    attachClickEventToPaginationLinks(); // Re-attach click events to new pagination links
}



function attachClickEventToPaginationLinks() {
    document.querySelectorAll('#pagination-links a').forEach(item => {
        item.addEventListener('click', function(e) {
            e.preventDefault();
            const page = this.getAttribute('href').split('page=')[1];
            fetchData(page);
        });
    });
}



</script>

@endsection

