@extends('layout')

@section('content')


<div class="container mt-5">
    <div class="row justify-content-center">
        <div class="col-md-6">
            <div class="tarjeta">
                <div class="encabezado-tarjeta">Bienvenido!</div>

                <div class="card-body">
                    <form method="POST" action="{{ route('login') }}">
                        @csrf

                        <div class="form-group">
                            <label for="email">Correo Electrónico</label>
                            <input id="email" type="email" class="form-control" name="email" value="{{ old('email') }}" required autocomplete="email" autofocus>
                            @error('email')
                            <span class="text-danger">{{ $message }}</span>
                            @enderror
                        </div>

                        <!-- Agregar esto en tu formulario -->
                        <div class="form-group">
                            <label for="password">Contraseña</label>
                            <div class="input-group">
                                <input id="password" type="password" class="form-control" name="password" required autocomplete="current-password">
                                <div class="input-group-append">
                                    <span class="input-group-text">
                                        <i class="fas fa-eye" id="togglePassword"></i>
                                    </span>
                                </div>
                            </div>
                            @error('password')
                            <span class="invalid-feedback" role="alert">
                                <strong>{{ $message }}</strong>
                            </span>
                            @enderror
                        </div>


                        <!-- <div class="form-group form-check">
                            <input type="checkbox" class="form-check-input" name="remember" id="remember" {{ old('remember') ? 'checked' : '' }}>
                            <label class="form-check-label" for="remember">Recordar Sesión</label>
                        </div>
                        -->
                        <div class="form-group d-flex justify-content-between">
                            <a href="{{ route('home') }}" class="btn boton-cancelar">Cancelar</a>
                            <button type="submit" class="btn boton-inicio">Iniciar Sesión</button>
                        </div>

                        @if (Route::has('password.request'))
                        <div class="form-group">
                            <a href="{{ route('password.request') }}">¿Olvidaste tu contraseña?</a>
                        </div>
                        @endif
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Agregar FontAwesome -->
<link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css" rel="stylesheet">
<script>
    const togglePassword = document.getElementById('togglePassword');
    const password = document.getElementById('password');

    togglePassword.addEventListener('click', function() {
        // Cambiar el tipo de entrada del campo de contraseña
        const type = password.getAttribute('type') === 'password' ? 'text' : 'password';
        password.setAttribute('type', type);
        // Cambiar el icono del ojo
        this.classList.toggle('fa-eye-slash');
    });
</script>
@endsection