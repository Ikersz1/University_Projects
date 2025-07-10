@extends('layout')

@section('content')
<!-- Primero jQuery -->
<!-- Bootstrap JS con Popper (de CDN oficial de Bootstrap) -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>

<!-- Luego Bootstrap JS (asegúrate de que incluya Popper si es necesario) -->
<script src="https://stackpath.bootstrapcdn.com/bootstrap/5.1.3/js/bootstrap.bundle.min.js"></script>

<a href="{{ route('admin.index') }}" class="btn boton-flecha">
    <i class="fa-solid fa-arrow-left-long fa-2xl"></i> <!-- Ícono de flecha -->
</a>


<div class="container">
    <h1 class="titulo-admin-Equipos"> Administración de Equipos</h1>

    <input type="text" id="search" placeholder="Buscar equipos..." onkeyup="fetchData()" class="form-control mb-3">
    <div class="d-flex justify-content-between" style="padding: 1rem;">
        <button class="btn boton-insertar-usuarios" onclick="insertarEquipos()">Insertar Equipos Aleatorios (Para pruebas)</button>
        <div>
            <button id="bulk-delete" class="btn btn-danger" onclick="deleteSelectedEquipos()">Eliminar Seleccionados</button>
            <button id="delete-all" class="btn btn-danger" onclick="deleteAllEquipos()">Eliminar Todos los Equipos</button>
        </div>
    </div>
    <div class="tarjeta">
        <div class="table-responsive">
            <table class="table">
                <thead>
                    <tr>
                        <th></th>
                        <th>Nombre</th>
                        <th>Liga</th>
                        <th>Ganados</th>
                        <th>Empatados</th>
                        <th>Perdidos</th>
                        <th>Partidos Jugados</th>
                        <th>GF</th>
                        <th>GC</th>
                        <th>Puntos</th>
                        <th>Acciones</th>
                    </tr>
                </thead>
                <tbody id="equipos-list">
                    <!-- Los equipos se llenan dinámicamente -->
                </tbody>
            </table>
            <div id="pagination-links" class="d-flex justify-content-center">
                <!-- Los enlaces de paginación se cargarán aquí -->
            </div>
        </div>
    </div>
</div>

<!-- Modal de Edición -->
<div class="modal fade" id="editEquipoModal" tabindex="-1" aria-labelledby="editEquipoModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="editEquipoModalLabel">Editar Equipo</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <!-- Formulario de edición -->
                <form id="editarEquipoForm">
                    <input type="hidden" id="edit_equipo_id" name="equipo_id">
                    <div class="mb-3">
                        <label for="edit_nombre" class="form-label">Nombre:</label>
                        <input type="text" class="form-control" id="edit_nombre" name="nombre" required>
                    </div>
                    <div class="mb-3">
                        <label for="edit_liga_id" class="form-label">ID de la Liga:</label>
                        <input type="number" class="form-control" id="edit_liga_id" name="liga_id">
                    </div>
                    <div class="mb-3">
                        <label for="edit_ganados" class="form-label">Ganados:</label>
                        <input type="number" class="form-control" id="edit_ganados" name="ganados">
                    </div>
                    <div class="mb-3">
                        <label for="edit_empatados" class="form-label">Empatados:</label>
                        <input type="number" class="form-control" id="edit_empatados" name="empatados">
                    </div>
                    <div class="mb-3">
                        <label for="edit_perdidos" class="form-label">Perdidos:</label>
                        <input type="number" class="form-control" id="edit_perdidos" name="perdidos">
                    </div>
                    <div class="mb-3">
                        <label for="edit_goles_favor" class="form-label">Goles a Favor:</label>
                        <input type="number" class="form-control" id="edit_goles_favor" name="goles_favor">
                    </div>
                    <div class="mb-3">
                        <label for="edit_goles_contra" class="form-label">Goles en Contra:</label>
                        <input type="number" class="form-control" id="edit_goles_contra" name="goles_contra">
                    </div>
                    <div class="mb-3">
                        <label for="edit_puntos" class="form-label">Puntos:</label>
                        <input type="number" class="form-control" id="edit_puntos" name="puntos">
                    </div>
                    <div class="mb-3">
                        <label for="edit_partidos_jugados" class="form-label">Partidos Jugados:</label>
                        <input type="number" class="form-control" id="edit_partidos_jugados" name="partidos_jugados">
                    </div>
                    <button type="submit" class="btn btn-primary">Actualizar Equipo</button>
                </form>
            </div>
        </div>
    </div>
</div>

<div class="container mt-5">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card mb-3">
                <div class="encabezado-tarjeta-usuarios">
                    Crear Nuevo Equipo
                </div>
                <div class="card-body">
                    <form id="crearEquipoForm">
                        <div class="mb-3">
                            <label for="nombre" class="form-label">Nombre:</label>
                            <input type="text" class="form-control" id="nombre" name="nombre" required>
                        </div>

                        <div class="mb-3">
                            <label for="ganados" class="form-label">Ganados:</label>
                            <input type="number" class="form-control" id="ganados" name="ganados">
                        </div>
                        <div class="mb-3">
                            <label for="empatados" class="form-label">Empatados:</label>
                            <input type="number" class="form-control" id="empatados" name="empatados">
                        </div>
                        <div class="mb-3">
                            <label for="perdidos" class="form-label">Perdidos:</label>
                            <input type="number" class="form-control" id="perdidos" name="perdidos">
                        </div>
                        <div class="mb-3">
                            <label for="goles_favor" class="form-label">Goles a Favor:</label>
                            <input type="number" class="form-control" id="goles_favor" name="goles_favor">
                        </div>
                        <div class="mb-3">
                            <label for="goles_contra" class="form-label">Goles en Contra:</label>
                            <input type="number" class="form-control" id="goles_contra" name="goles_contra">
                        </div>
                        <div class="mb-3">
                            <label for="puntos" class="form-label">Puntos:</label>
                            <input type="number" class="form-control" id="puntos" name="puntos">
                        </div>
                        <div class="mb-3">
                            <label for="partidos_jugados" class="form-label">Partidos Jugados:</label>
                            <input type="number" class="form-control" id="partidos_jugados" name="partidos_jugados">
                        </div>
                        <!-- Añade más campos según necesites -->
                        <div class="d-flex justify-content-end">
                            <button type="submit" class="btn boton-crear-equipo">Crear Equipo</button>
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

    // Añadir evento submit al formulario de creación de equipo
    document.getElementById('crearEquipoForm').addEventListener('submit', function(event) {
        event.preventDefault(); // Evita que el formulario se envíe de forma predeterminada
        const formData = new FormData(this);
        fetch('/adminequipos/crear', {
                method: 'POST',
                body: formData,
                headers: {
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                },
            })
            .then(response => response.json())
            .then(data => {
                alert(data.message); // Mostrar un mensaje de éxito o error
                fetchData(); // Recargar la lista de equipos
                this.reset(); // Restablecer el formulario después de la creación exitosa
            })
            .catch(error => console.error('Error:', error));
    });

    // Añadir evento submit al formulario de edición de equipo
    document.getElementById('editarEquipoForm').addEventListener('submit', function(event) {
        event.preventDefault();
        const equipoId = document.getElementById('edit_equipo_id').value;
        const formData = new FormData(this);
        fetch(`/adminequipos/actualizar/${equipoId}`, {
                method: 'POST',
                body: formData,
                headers: {
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                },
            })
            .then(response => response.json())
            .then(data => {
                alert(data.message);
                $('#editEquipoModal').modal('hide');
                fetchData(); // Recargar los datos de la tabla
            })
            .catch(error => console.error('Error:', error));
    });

    // Función para cargar los datos de equipos
    function fetchData(page = 1) {
        var search = document.getElementById('search').value;
        var url = `/adminequipos?search=${search}&page=${page}`;
        fetch(url, {
                method: 'GET',
                headers: {
                    'X-Requested-With': 'XMLHttpRequest',
                },
            })
            .then(response => response.json())
            .then(data => {
                var tableBody = document.getElementById('equipos-list');
                tableBody.innerHTML = ''; // Limpieza del cuerpo de la tabla
                data.data.forEach(equipo => {
                    var row = `<tr>
                <td><input type="checkbox" class="equipo-checkbox" value="${equipo.id}"></td>
                <td>${equipo.nombre}</td>
                <td>${equipo.liga_id}</td>
                <td>${equipo.ganados}</td>
                <td>${equipo.empatados}</td>
                <td>${equipo.perdidos}</td>
                <td>${equipo.partidos_jugados}</td>
                <td>${equipo.goles_favor}</td>
                <td>${equipo.goles_contra}</td>
                <td>${equipo.puntos}</td>
                <td>
                    <button onclick="openEditModal(${equipo.id})" class="btn boton-editar"><i class="fas fa-pencil-alt"></i></button>
                    <button class="btn btn-eliminar" onclick="deleteEquipo(${equipo.id})"><i class="fas fa-trash-alt" style="color: red;"></i></button>
                </td>
            </tr>`;
                    tableBody.innerHTML += row;
                });

                var paginationDiv = document.getElementById('pagination-links');
                paginationDiv.innerHTML = data.links;
                attachClickEventToPaginationLinks(); // Asegúrate de que esta función esté definida
            })
            .catch(error => console.error('Error:', error));
    }
    // Función para adjuntar eventos de clic a los enlaces de paginación
    function attachClickEventToPaginationLinks() {
        document.querySelectorAll('#pagination-links a').forEach(item => {
            item.addEventListener('click', function(e) {
                e.preventDefault(); // Evita la navegación directa
                const page = this.getAttribute('href').split('page=')[1];
                fetchData(page);
            });
        });
    }

    // Abre el modal de edición cargando los datos del equipo seleccionado
    function openEditModal(equipoId) {
        fetch(`/adminequipos/datos/${equipoId}`)
            .then(response => response.json())
            .then(data => {
                document.getElementById('edit_equipo_id').value = data.id;
                document.getElementById('edit_nombre').value = data.nombre;
                document.getElementById('edit_liga_id').value = data.liga_id || ''; // Use || '' para manejar nulls
                document.getElementById('edit_ganados').value = data.ganados;
                document.getElementById('edit_empatados').value = data.empatados;
                document.getElementById('edit_perdidos').value = data.perdidos;
                document.getElementById('edit_goles_favor').value = data.goles_favor;
                document.getElementById('edit_goles_contra').value = data.goles_contra;
                document.getElementById('edit_puntos').value = data.puntos;
                document.getElementById('edit_partidos_jugados').value = data.partidos_jugados;

                var editModal = new bootstrap.Modal(document.getElementById('editEquipoModal'));
                editModal.show();
            })
            .catch(error => console.error('Error:', error));
    }




    // Eventos para la paginación


    // Elimina todos los equipos
    function deleteAllEquipos() {
        if (confirm('¿Estás seguro de querer eliminar TODOS los equipos? Esta acción es irreversible.')) {
            fetch('/adminequipos/eliminar-todos', {
                    method: 'DELETE',
                    headers: {
                        'X-Requested-With': 'XMLHttpRequest',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                    },
                })
                .then(response => response.json())
                .then(data => {
                    alert(data.message);
                    fetchData(); // Recargar la lista de equipos
                })
                .catch(error => console.error('Error:', error));
        }
    }

    // Elimina un equipo específico
    function deleteEquipo(equipoId) {
        if (confirm('¿Seguro que quieres eliminar el equipo?')) {
            fetch(`/adminequipos/eliminar/${equipoId}`, {
                    method: 'DELETE',
                    headers: {
                        'X-Requested-With': 'XMLHttpRequest',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                    },
                })
                .then(response => {
                    if (response.ok) {
                        alert('Equipo eliminado con éxito');
                        fetchData();
                    } else {
                        response.json().then(data => alert(data.message));
                    }
                })
                .catch(error => console.error('Error:', error));
        }
    }

    // Elimina los equipos seleccionados
    function deleteSelectedEquipos() {
        let selectedIds = Array.from(document.querySelectorAll('.equipo-checkbox:checked')).map(el => el.value);
        if (selectedIds.length === 0) {
            alert('Por favor, selecciona al menos un equipo para eliminar.');
            return;
        }

        if (!confirm('¿Seguro que quieres eliminar los equipos seleccionados?')) return;

        fetch('/adminequipos/eliminar-masa', {
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
            })
            .catch(error => console.error('Error:', error));
    }
</script>




@endsection