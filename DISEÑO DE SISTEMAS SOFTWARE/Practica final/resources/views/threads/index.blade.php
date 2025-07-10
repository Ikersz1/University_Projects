@extends('layout')

@section('content')
<div class="jumbotron jumbotron-fluid">
    <div class="container-fluid">
        <h1 class="display-4 font-weight-bold text-center" style="font-size: 2rem; color:white;">Foro de La Liga 2023/24</h1>
    </div>
</div>

<style>
    .container {
        padding-top: 20px;
    }

    .btn-primary,
    .btn-secondary {
        width: 200px;
    }

    .card {
        margin-top: 10px;
        box-shadow: 0 4px 8px 0 rgba(0, 0, 0, 0.2);
        transition: 0.3s;
    }

    .card:hover {
        box-shadow: 0 8px 16px 0 rgba(0, 0, 0, 0.2);
    }

    .card-header {
        background-color: #495057;
        /* Un gris más oscuro para mayor contraste */
        color: #ffffff;
        font-size: 20px;
        padding: 10px 15px;
        border-top-left-radius: 8px;
        border-top-right-radius: 8px;
    }

    .btn-primary {
        color: #fff;
        background-color: #17a2b8;
        /* Un azul diferente para el botón Ver Hilo */
        border-color: #17a2b8;
    }

    .card-body {
        padding: 20px;
    }

    .search-bar {
        margin-bottom: 20px;
    }
</style>

<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="d-flex justify-content-between">
                <a href="{{ route('threads.create') }}" class="btn boton-insertar-usuarios mb-3">Crear un Hilo</a>

                <button id="toggleMyThreads" class="btn btn-secondary mb-3" data-mine="false">Ver Mis Hilos</button>
            </div>
            <!-- Search bar -->
            <input type="text" id="search" placeholder="Buscar hilos o usuarios..." class="form-control search-bar">

            <div id="threadsContainer">

                @include('threads.thread_list', ['threads' => $threads])

            </div>
        </div>
    </div>
</div>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        const searchInput = document.getElementById('search');
        const toggleBtn = document.getElementById('toggleMyThreads');
        let showMyThreads = false; // Initialize as false

        toggleBtn.addEventListener('click', () => {
            showMyThreads = !showMyThreads; // Toggle the boolean value
            toggleBtn.setAttribute('data-mine', showMyThreads.toString()); // Update the attribute
            toggleBtn.textContent = showMyThreads ? 'Ver Todos los Hilos' : 'Ver Mis Hilos'; // Update the button text
            fetchMyThreads(); // Fetch threads based on the new filter
        });

        function fetchThreads() {
            const searchTerm = searchInput.value;
            fetch(`{{ url('/threads/search') }}?search=${searchTerm}&showMy=false`)
                .then(response => response.text())
                .then(html => {
                    document.getElementById('threadsContainer').innerHTML = html;
                    attachDeleteHandlers();
                })
                .catch(error => console.error('Error loading the threads:', error));
        }

        function fetchMyThreads() {
            fetch(`{{ url('/toggleThreads') }}?showMy=${showMyThreads}`)
                .then(response => {
                    if (response.status === 401) { // Si el usuario no está autenticado
                        window.location.href = "{{ route('login') }}"; // Redirige al usuario a la página de inicio de sesión
                    }
                    return response.text(); // Procesa el resto de la respuesta como texto
                })
                .then(html => {
                    document.getElementById('threadsContainer').innerHTML = html; // Actualiza el HTML del contenedor
                    attachDeleteHandlers(); // Vuelve a enganchar los manejadores de eventos
                })
                .catch(error => console.error('Error loading the threads:', error));
        }



        function attachDeleteHandlers() {
            document.querySelectorAll('.delete-btn').forEach(button => {
                button.removeEventListener('click', handleDelete);
                button.addEventListener('click', handleDelete);
            });
        }

        function handleDelete() {
            if (confirm('¿Estás seguro de querer eliminar este hilo?')) {
                const threadId = this.getAttribute('data-thread-id');
                fetch(`{{ route('threads.destroy', '') }}/${threadId}`, {
                        method: 'POST',
                        headers: {
                            'X-CSRF-TOKEN': '{{ csrf_token() }}',
                            'Content-Type': 'application/json'
                        },
                        body: JSON.stringify({
                            _method: 'DELETE'
                        })
                    })
                    .then(response => {
                        if (response.ok) {
                            document.getElementById('thread-' + threadId).remove();
                            fetchMyThreads();
                        }
                    })
                    .catch(error => console.error('Error:', error));
            }
        }

        searchInput.addEventListener('keyup', fetchThreads);

        fetchThreads(); // Initial fetch of threads
    });
</script>

@endsection