<style>
    .card {
        margin-top: 10px;
        background-color: #f9f9f9;
        border: none;
        border-radius: 8px;
        box-shadow: 0 2px 5px rgba(0,0,0,0.1);
        transition: box-shadow 0.3s ease-in-out;
    }
    .card:hover {
        box-shadow: 0 5px 15px rgba(0,0,0,0.2);
    }
    .card-header {
    background-color: #f8f9fa; /* Nuevo color gris claro */
    color: #333; /* Cambiando el color del texto a gris oscuro para mejor contraste */
    font-size: 20px;
    padding: 10px 15px;
    border-top-left-radius: 8px;
    border-top-right-radius: 8px;
}

    .card-body {
        padding: 15px;
        line-height: 1.5;
        color: #333;
    }
    .card-footer {
        background-color: #f8f9fa;
        padding: 10px 15px;
        border-bottom-left-radius: 8px;
        border-bottom-right-radius: 8px;
    }
    .btn-primary {
        color: #fff;
        background-color: #007bff;
        border-color: #007bff;
    }
    .btn-danger {
        background-color: #dc3545;
        border-color: #dc3545;
    }
    .btn {
        margin-right: 5px;
    }
</style>

@foreach ($threads as $thread)
    <div class="card mb-3" id="thread-{{ $thread->id }}">
        <div class="card-header">
            <h4>{{ $thread->topic }}</h4>
        </div>
        <div class="card-body">
            <p>{{ substr($thread->content, 0, 100) }}{{ strlen($thread->content) > 100 ? '...' : '' }}</p>
            <small>Publicado por {{ $thread->user->name }} el {{ $thread->created_at->format('d/m/Y H:i') }}</small>
        </div>
        <div class="card-footer">
            <!-- Botón para ver detalles del hilo, disponible para todos los usuarios -->
            <a href="{{ route('threads.show', $thread->id) }}" class="btn btn-primary">Ver Hilo</a>

            @auth
                <!-- Condicionales para mostrar el botón de eliminar -->
                @if (auth()->user()->id == $thread->user_id || (Auth::user()->role->name === 'admin' && auth()->user()->id != $thread->user_id))
                    <button type="button" class="btn btn-danger delete-btn" data-thread-id="{{ $thread->id }}">Eliminar</button>
                @endif
            @endauth
        </div>
    </div>
@endforeach

