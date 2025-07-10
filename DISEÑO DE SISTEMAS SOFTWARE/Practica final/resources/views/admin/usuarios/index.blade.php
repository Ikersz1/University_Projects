@extends('layout')

@section('content')
<a href="{{ route('admin.index') }}" class="btn boton-flecha">
    <i class="fa-solid fa-arrow-left-long fa-2xl"></i> <!-- Ícono de flecha -->
</a>

<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-8 position-relative">

            <!-- Botón de retorno -->

            <!-- Formulario de búsqueda -->
            <form action="{{ route('admin.usuarios.index') }}" method="GET" class="mb-3 d-flex justify-content-end">
                <input type="text" name="salida" class="buscador" placeholder="Buscar por nombre o correo" value="{{ request('salida') }}">
                <button type="submit" class="btn boton-buscar">Buscar</button>
            </form>



            <!-- Tarjeta de agregar usuarios -->
            <div class="tarjeta-agregar-usuarios mb-3 boton-insertar-usuarios">
                <a href="{{ route('admin.usuarios.create') }}" class="btn boton-agregar">Agregar Usuario</a>
            </div>

            <!-- Botones para eliminar usuarios -->
            <div class="d-flex justify-content-between mb-3">
                <form action="{{ route('admin.usuarios.eliminar-seleccionados') }}" method="POST" id="formEliminarSeleccionados">
                    @csrf
                    <input type="hidden" name="usuarios_seleccionados" id="usuariosSeleccionados">
                    <button type="button" class="btn btn-danger" id="eliminarSeleccionados" onclick="return confirm('¿Estás seguro de que deseas eliminar los usuarios seleccionados?')">Eliminar Seleccionados</button>
                </form>
                <form action="{{ route('admin.usuarios.eliminar-todos') }}" method="POST" id="formEliminarTodos">
                    @csrf
                    <button type="submit" class="btn btn-danger" onclick="return confirm('¿Estás seguro de que deseas eliminar todos los usuarios excepto el administrador?')">Eliminar Todos</button>
                </form>
            </div>


            <!-- Tarjeta de usuarios -->
            <div class="tarjeta mb-3">
                <div class="encabezado-tarjeta-usuarios">Lista de Usuarios</div>
                <div class="card-body">
                    <table class="table">
                        <thead>
                            <tr style="margin-bottom: auto;">
                                <th>Seleccionar</th>
                                <th>
                                    <button id="ordenarNombre" class="btn boton-nombre">Nombre </button>
                                </th>
                                <th>Correo</th>
                                <th>Acciones</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($usuarios as $usuario)
                            <tr>
                                <td>
                                    @if (!in_array($usuario->role->name, ['admin', 'noticiero', 'analista']))
                                    <input type="checkbox" name="usuarios_seleccionados[]" value="{{ $usuario->id }}" class="checkbox-red">
                                    @endif
                                </td>
                                <td>{{ $usuario->name }}</td>
                                <td>{{ $usuario->email }}</td>
                                <td>
                                    <a href="{{ route('admin.usuarios.edit', $usuario->id) }}" class="btn boton-editar">
                                        <i class="fas fa-pencil-alt"></i> <!-- Ícono de lápiz -->
                                    </a>
                                    @if(!in_array($usuario->role->name, ['admin', 'noticiero', 'analista']))
                                    <form action="{{ route('admin.usuarios.destroy', ['id' => $usuario->id]) }}" method="POST" style="display: inline;">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="btn btn-link text-danger btn-eliminar" onclick="return confirm('¿Estás seguro de que deseas eliminar este usuario?')">
                                            <i class="fas fa-trash-alt"></i> <!-- Ícono de la papelera -->
                                        </button>
                                    </form>
                                    @endif
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
            <!-- Agrega la paginación aquí -->
            <div id="pagination-links" class="d-flex justify-content-center">
                {{ $usuarios->links() }}
            </div>
        </div>
    </div>
</div>

@if(Session::has('success'))
<script>
    window.onload = function() {
        alert("{{ Session::get('success') }}");
    };
</script>
@endif

<script>
    $(document).ready(function() {
        $('.checkbox-red').change(function() {
            if ($(this).is(':checked')) {
                $(this).addClass('checked-red');
            } else {
                $(this).removeClass('checked-red');
            }
        });
    });
</script>
<script>
    document.addEventListener('DOMContentLoaded', function() {
        const botonOrdenarNombre = document.getElementById('ordenarNombre');
        let ordenAscendenteNombre = true;

        botonOrdenarNombre.addEventListener('click', function() {
            // Cambia la dirección del orden para el nombre
            ordenAscendenteNombre = !ordenAscendenteNombre;

            // Redirige con el nuevo parámetro de orden solo para el nombre y el estado actual para el correo electrónico
            window.location.href = '{{ route("admin.usuarios.index") }}' + '?ordenNombre=' + (ordenAscendenteNombre ? 'asc' : 'desc');
        });

        // Verificar si hay parámetros de orden en la URL y establecer el estado del ordenAscendente en consecuencia solo para el nombre
        const urlParams = new URLSearchParams(window.location.search);
        const ordenNombreParam = urlParams.get('ordenNombre');
        if (ordenNombreParam === 'desc') {
            ordenAscendenteNombre = false;
        }
    });
</script>


<script>
    document.addEventListener('DOMContentLoaded', function() {
        const botonEliminarSeleccionados = document.getElementById('eliminarSeleccionados');
        const formEliminarSeleccionados = document.getElementById('formEliminarSeleccionados');
        const checkboxes = document.querySelectorAll('.checkbox-red');

        let confirmacionMostrada = false;

        botonEliminarSeleccionados.addEventListener('click', function(event) {
            const usuariosSeleccionados = [];
            checkboxes.forEach(function(checkbox) {
                if (checkbox.checked) {
                    usuariosSeleccionados.push(checkbox.value);
                }
            });

            // Si no hay ningún checkbox seleccionado, mostrar mensaje y cancelar el evento
            if (usuariosSeleccionados.length === 0) {
                alert('No has seleccionado ningún usuario para eliminar.');
                event.preventDefault();
                return false;
            }

            // Confirmar la eliminación solo si la confirmación no se ha mostrado
            /* if (!confirmacionMostrada) {
                if (!confirm('¿Estás seguro de que deseas eliminar los usuarios seleccionados?')) {
                    event.preventDefault();
                    return false;
                }
                confirmacionMostrada = true; // Marcar que la confirmación se ha mostrado
            } */

            // Establecer los IDs seleccionados en el campo oculto
            document.getElementById('usuariosSeleccionados').value = usuariosSeleccionados.join(',');

            // Enviar el formulario de eliminación de usuarios seleccionados
            formEliminarSeleccionados.submit();
        });
    });
</script>



@endsection