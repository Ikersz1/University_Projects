@extends('layout')

@section('content')
<head> 
    <meta name="csrf-token" content="{{ csrf_token() }}">
</head>

<a href="{{ route('admin.index') }}" class="btn boton-flecha">
    <i class="fa-solid fa-arrow-left-long fa-2xl"></i> <!-- Ícono de flecha -->
</a>

<div class="container">
    <h1 style="text-align: center; margin: 1rem;">Administración de Jugadores</h1>
    @if (session('success'))
        <div class="alert alert-success" role="alert">
            {{ session('success') }}
        </div>
    @endif

    @if (session('error'))
        <div class="alert alert-danger" role="alert">
            {{ session('error') }}
        </div>
    @endif
    <input type="text" id="search" placeholder="Buscar jugadores..." onkeyup="fetchData()" class="form-control mb-3">
    <div class="d-flex justify-content-between" style="padding: 1rem;">
        <button class="btn boton-insertar-usuarios" onclick="insertarJugadores()">Insertar Jugadores Aleatorios (Para pruebas)</button>
        <div>
            <button id="bulk-delete" class="btn btn-danger" onclick="deleteSelectedJugadores()">Eliminar Seleccionados</button>
            <button id="delete-all" class="btn btn-danger" onclick="deleteAllJugadores()">Eliminar Todos los Jugadores</button>
        </div>
    </div>

    <div class="table-responsive">
        <table class="table">
            <thead>
                <tr>
                    <th></th>
                    <th>ID</td>
                    <th>Nombre</th>
                    <th>Acciones</th>
                </tr>
            </thead>
            <tbody id="jugadores-list">
                <!-- Los jugadores se llenan dinámicamente -->
            </tbody>
        </table>
        <div id="pagination-links" class="d-flex justify-content-center">
            <!-- Los enlaces de paginación se cargarán aquí -->
        </div>
    </div>
</div>

<div class="container mt-5">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card mb-3">
                <div class="encabezado-tarjeta-usuarios">
                    Crear Nuevo Jugador
                </div>
                <div class="card-body">
                    <form id="crearJugadorForm" method="POST" action="{{ route('adminjugador.crear') }}" enctype="multipart/form-data">
                        @csrf
                        <div class="mb-3">
                            <label for="nombre" class="form-label">Nombre:</label>
                            <input type="text" class="form-control" id="nombre" name="nombre" required>
                        </div>

                        <div class="mb-3">
                            <label for="posicion" class="form-label">Posición:</label>
                            <input type="text" class="form-control" id="posicion" name="posicion">
                        </div>
                        <div class="mb-3">
                            <label for="nacionalidad" class="form-label">Nacionalidad:</label>
                            <input type="text" class="form-control" id="nacionalidad" name="nacionalidad">
                        </div>
                        <div class="mb-3">
                            <label for="edad" class="form-label">Edad:</label>
                            <input type="number" class="form-control" id="edad" name="edad">
                        </div>
                        <div class="mb-3">
                            <label for="equipo_id" class="form-label">Equipo:</label>
                            <select class="form-select" id="equipo_id" name="equipo_id">
                                @foreach ($equipos as $equipo)
                                    <option value="{{ $equipo->id }}">{{ $equipo->nombre }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="mb-3">
                            <label for="foto" class="form-label">Foto:</label>
                            <input type="file" class="form-control" id="foto" name="foto">
                        </div>
                        <div class="mb-3">
                            <label for="biografia" class="form-label">Biografía:</label>
                            <textarea class="form-control" id="biografia" name="biografia"></textarea>
                        </div>

                        <div class="d-flex justify-content-end">
                            <button type="submit" class="btn boton-crear-jugador">Crear Jugador</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    fetchData(); // Carga inicial de datos
});

function fetchData(page = 1) {
    var search = document.getElementById('search').value;
    var url = `/adminjugadores?search=${search}&page=${page}`;
    fetch(url, {
        method: 'GET',
        headers: {
            'X-Requested-With': 'XMLHttpRequest',
        },
    })
    .then(response => response.json())
    .then(data => {
        var tableBody = document.getElementById('jugadores-list');
        tableBody.innerHTML = '';
        data.data.forEach(jugador => {
            var row = `<tr>
                <td><input type="checkbox" class="jugador-checkbox" value="${jugador.id}"></td>
                <td>${jugador.id}</td>
                <td>${jugador.nombre}</td>
                <td>
                    <a href="/adminjugadores/jugadores/editar/${jugador.id}" class="btn boton-editar"><i class="fas fa-pencil-alt"></i></a>
                    <button class="btn btn-eliminar" onclick="deleteJugador(${jugador.id})"><i class="fas fa-trash-alt" style="color: red;"></i></button>
                </td>
            </tr>`;
            tableBody.innerHTML += row;
        });
        attachCheckboxEvents(); // Adjuntar eventos a los nuevos checkboxes
        checkSelectedCheckboxes();
        var paginationDiv = document.getElementById('pagination-links');
        paginationDiv.innerHTML = ''; // Limpiar antes de añadir los nuevos enlaces
        paginationDiv.innerHTML = data.links; // Añadir los nuevos enlaces de paginación
        attachClickEventToPaginationLinks();
    })
    .catch(error => {
        console.error('Error:', error);
    });
}

function attachClickEventToPaginationLinks() {
    document.querySelectorAll('#pagination-links a').forEach(item => {
        item.addEventListener('click', function(e) {
            e.preventDefault(); // Evita la navegación directa
            const page = this.getAttribute('href').split('page=')[1];
            fetchData(page);
        });
    });
}

function deleteJugador(jugadorId) {
    // Mostrar un mensaje de confirmación antes de eliminar
    if (confirm('¿Seguro que quieres eliminar al jugador?')) {
        // Si el usuario confirma, proceder con la eliminación
        eliminarJugador(jugadorId);
    }
}

function eliminarJugador(jugadorId) {
    fetch(`/adminjugadores/eliminar/${jugadorId}`, {
        method: 'POST',
        headers: {
            'X-Requested-With': 'XMLHttpRequest',
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
        },
    })
    .then(response => {
        if (response.ok) {
            fetchData(); // Recargar los datos para actualizar la lista
            alert('Jugador eliminado con éxito');
        } else {
            response.json().then(data => alert(data.message));
        }
    })
    .catch(error => {
        console.error('Error:', error);
    });
}

function deleteSelectedJugadores() {
    const selectedIds = JSON.parse(localStorage.getItem('selectedJugadores')) || [];

    if (selectedIds.length === 0) {
        alert('Por favor, selecciona al menos un jugador para eliminar.');
        return;
    }

    if (!confirm('¿Seguro que quieres eliminar a los jugadores seleccionados?')) return;

    fetch('/adminjugadores/eliminar-masa', {
        method: 'POST',
        headers: {
            'X-Requested-With': 'XMLHttpRequest',
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
            'Content-Type': 'application/json',
        },
        body: JSON.stringify({
            ids: selectedIds
        }),
    })
    .then(response => response.json())
    .then(data => {
        alert(data.message);
        fetchData(); // Recargar la lista para reflejar los cambios
        localStorage.setItem('selectedJugadores', JSON.stringify([])); // Limpiar las selecciones después de la eliminación
    })
    .catch(error => console.error('Error:', error));
}

function attachCheckboxEvents() {
    document.querySelectorAll('.jugador-checkbox').forEach(checkbox => {
        checkbox.addEventListener('change', function() {
            let selectedIds = JSON.parse(localStorage.getItem('selectedJugadores')) || [];
            if (this.checked) {
                selectedIds.push(this.value);
            } else {
                selectedIds = selectedIds.filter(id => id !== this.value);
            }
            localStorage.setItem('selectedJugadores', JSON.stringify(selectedIds));
        });
    });
}

function checkSelectedCheckboxes() {
    const selectedIds = JSON.parse(localStorage.getItem('selectedJugadores')) || [];
    document.querySelectorAll('.jugador-checkbox').forEach(checkbox => {
        checkbox.checked = selectedIds.includes(checkbox.value);
    });
}

document.getElementById('crearJugadorForm').addEventListener('submit', function(e) {
    e.preventDefault();

    const formData = new FormData(this);
    fetch('{{ route('adminjugador.crear') }}', {
        method: 'POST',
        body: formData,
        headers: {
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
        },
    })
    .then(response => response.json())
    .then(data => {
        if (data.message) {
            alert(data.message);
        }
        if (data.jugador) {
            fetchData(); // Recargar la lista de jugadores
        }
    })
    .catch(error => console.error('Error:', error));
});

function deleteAllJugadores() {
    if (confirm('¿Estás seguro de querer eliminar TODOS los jugadores y sus estadísticas? Esta acción es irreversible.')) {
        fetch('/adminjugadores/eliminar-todos', {
            method: 'POST',
            headers: {
                'X-Requested-With': 'XMLHttpRequest',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                'Content-Type': 'application/json',
            },
        })
        .then(response => response.json())
        .then(data => {
            alert(data.message);
            fetchData(); // Recargar los datos después de eliminar
        })
        .catch(error => console.error('Error:', error));
    }
}

function confirmarInsertarJugadores() {
    if (confirm('¿Estás seguro de que quieres insertar jugadores aleatorios? Esta acción no se puede deshacer.')) {
        insertarJugadores();
    }
}

function insertarJugadores() {
    fetch('/adminjugadores/insertar-jugadores', {
        method: 'POST',
        headers: {
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
            'Content-Type': 'application/json'
        },
        body: JSON.stringify({})
    })
    .then(response => {
        if (!response.ok) {
            console.error('Network response was not ok, status:', response.status);
            return response.text();
        }
        return response.json();
    })
    .then(data => {
        if (typeof data === 'string') {
            console.error('Error message from server:', data);
            alert('Error al insertar jugadores: ' + data);
        } else {
            alert(data.message);
            fetchData(); // Recargar la lista de jugadores
        }
    })
    .catch(error => {
        console.error('Error:', error);
        alert('Error al insertar jugadores.');
    });
}
</script>

@endsection
