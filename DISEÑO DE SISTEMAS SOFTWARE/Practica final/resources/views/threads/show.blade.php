@extends('layout')

@section('content')
<style>
    .container {
        max-width: 800px;
        margin: auto;
        padding-top: 20px;
    }
    .card {
        background-color: #fff;
        border-radius: 8px;
        box-shadow: 0 2px 6px rgba(0,0,0,0.1);
        margin-bottom: 20px;
        overflow: hidden;
    }
    .card-header {
        background-color: #007bff;
        color: #ffffff;
        padding: 10px 20px;
        font-size: 24px;
        font-weight: bold;
    }
    .card-body {
        padding: 20px;
        line-height: 1.6;
        color: #333;
    }
    .card-body small {
        display: block;
        margin-top: 10px;
        color: #666;
    }
    textarea {
        width: 100%;
        height: 100px;
        padding: 10px;
        margin-top: 10px;
        border: 1px solid #ccc;
        border-radius: 4px;
    }
    button {
        display: block;
        background-color: #28a745;
        color: white;
        padding: 10px 20px;
        border: none;
        border-radius: 4px;
        margin-top: 10px;
        cursor: pointer;
    }
    button:hover {
        background-color: #218838;
    }
</style>

<div class="container">
    <div class="card">
        <div class="card-header">
            <h4>{{ $thread->topic }}</h4>
        </div>
        <div class="card-body" style="overflow-x: auto;">
    <pre style="white-space: pre-wrap;">{{ $thread->content }}</pre>
    <small>Publicado por {{ $thread->user->name }} el {{ $thread->created_at->format('d/m/Y H:i') }}</small>
</div>


        <div class="response-form">
            <form method="POST" action="{{ route('responses.store', $thread->id) }}">
                @csrf
                <textarea name="content" placeholder="Escribe tu respuesta aquí..."></textarea>
                <button type="submit">Responder</button>
            </form>
        </div>
        </div>
        <hr>
        
        @foreach ($thread->responses->whereNull('parent_id') as $response)
            @include('threads.show_responses', ['response' => $response, 'level' => 0])
        @endforeach
    
</div>
@endsection
