@extends('layout')

@section('content')
<div class="container">
    <h2>Usuarios que han creado hilos</h2>
    <ul>
        @foreach ($users as $user)
            <li>{{ $user->name }} - {{ $user->threads->count() }} hilos creados</li>
        @endforeach
    </ul>
</div>
@endsection
</script>