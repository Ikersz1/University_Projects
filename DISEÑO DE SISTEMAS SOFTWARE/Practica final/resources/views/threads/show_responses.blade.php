
<style>
    .response-block {
    background-color: #f9f9f9; /* Color de fondo para destacar */
    margin-top: 10px;
    padding: 10px;
    border-radius: 5px; /* Bordes redondeados para sub-respuestas */
    
}
.delete-btn {
        background-color: #dc3545; /* Color rojo para botones de eliminar */
        color: white; /* Texto blanco para mejor contraste */
        padding: 10px 20px;
        border: none;
        border-radius: 4px;
        cursor: pointer;
    }
    .delete-btn:hover {
        background-color: #c82333; /* Color más oscuro al pasar el mouse */
    }
    .response-block {
        background-color: #f9f9f9;
        margin-top: 10px;
        padding: 10px;
        border-radius: 5px;
    }
</style>
@if ($level == 0)
<div class="card mt-3">
@endif

<div class="response-block" style="margin-left: {{ 20 * $level }}px; padding: 20px; border-left: 2px solid #ccc;">
    <pre style="white-space: pre-wrap;">{{ $response->content }}</pre>
    <small>Respondido por {{ $response->user->name }} el {{ $response->created_at->format('d/m/Y H:i') }}</small>

    @if (auth()->check() && (auth()->user()->id == $response->user_id || Auth::user()->role->name === 'admin'))   
    <form method="POST" action="{{ route('responses.destroy', $response->id) }}">
            @csrf
            @method('DELETE')
            <button type="submit" class="delete-btn" onclick="return confirm('¿Estás seguro de querer eliminar esta respuesta?');">Eliminar</button>
    </form>
    @endif

    <form method="POST" action="{{ route('responses.store', $thread->id) }}">
        @csrf
        <input type="hidden" name="parent_id" value="{{ $response->id }}">
        <textarea name="content" placeholder="Responder a este mensaje..."></textarea>
        <button type="submit">Responder</button>
    </form>

    @foreach ($response->children as $child)
        @include('threads.show_responses', ['response' => $child, 'level' => $level + 1])
    @endforeach
</div>

@if ($level == 0)
</div>
@endif
