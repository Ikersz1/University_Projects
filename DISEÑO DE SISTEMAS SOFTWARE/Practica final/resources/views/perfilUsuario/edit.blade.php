@extends('layout')

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="tarjeta">
                <div class="encabezado-tarjeta-usuarios">Actualizar información</div>
                <div class="card-body">
                    <form action="{{ route('perfilUsuario.update', $user->id) }}" method="POST" enctype="multipart/form-data">
                        @csrf
                        @method('PUT')
                        <div class="form-group">
                            <label for="nombre">Nuevo nombre:</label>
                            <input type="text" class="form-control" id="name" name="name" value="{{ $user->name }}">
                        </div>
                        <div class="form-group">
                            <label for="email">Email:</label>
                            <input type="email" class="form-control" id="email" name="email" value="{{ $user->email }}" readonly style="background-color: white">
                        </div>
                        <div class="form-group">
                            <label for="password">Nueva Contraseña:</label>
                            <div class="input-group">
                                <input type="password" class="form-control @error('password') is-invalid @enderror" id="password" name="password">
                                <div class="input-group-append">
                                    <span class="input-group-text">
                                        <i class="fas fa-eye" id="togglePassword"></i>
                                    </span>
                                </div>
                            </div>
                        </div>
                        <div class="form-group">
                            <label for="password_confirmation">Confirmar Nueva Contraseña:</label>
                            <div class="input-group">
                                <input type="password" class="form-control @error('password') is-invalid @enderror" id="password_confirmation" name="password_confirmation">
                                <div class="input-group-append">
                                    <span class="input-group-text">
                                        <i class="fas fa-eye" id="togglePasswordConfirmation"></i>
                                    </span>
                                </div>
                            </div>
                        </div>
                        <!-- Mostrar el campo de carga de foto de perfil solo si el usuario no tiene una foto -->

                        <div class="form-group">
                            <label for="profile_picture">Foto de Perfil (opcional)</label>
                            <input id="profile_picture" type="file" class="form-control @error('profile_picture') is-invalid @enderror" name="profile_picture" accept="image/*">
                            @error('profile_picture')
                            <span class="invalid-feedback" role="alert">
                                <strong>{{ $message }}</strong>
                            </span>
                            @enderror
                            <div class="mt-2">
                                <img id="profilePicturePreview" src="#" alt="Vista previa de la foto de perfil" class="img-thumbnail" style="display: none; max-width: 150px;">
                            </div>
                        </div>


                        <div class="form-group d-flex justify-content-between">
                            <a href="{{ route('home') }}" class="btn boton-cancelar">Cancelar</a>
                            <button type="submit" class="btn boton-actualizar-usuarios btn-outline-light ml-auto">Guardar Cambios</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">

<script>
    const togglePassword = document.querySelector('#togglePassword');
    const togglePasswordConfirmation = document.querySelector('#togglePasswordConfirmation');
    const password = document.querySelector('#password');
    const passwordConfirmation = document.querySelector('#password_confirmation');

    togglePassword.addEventListener('click', function(e) {
        // Cambiar el tipo de entrada del campo de contraseña
        const type = password.getAttribute('type') === 'password' ? 'text' : 'password';
        password.setAttribute('type', type);
        // Cambiar el icono del ojo
        this.classList.toggle('fa-eye-slash');
    });

    togglePasswordConfirmation.addEventListener('click', function(e) {
        // Cambiar el tipo de entrada del campo de confirmación de contraseña
        const type = passwordConfirmation.getAttribute('type') === 'password' ? 'text' : 'password';
        passwordConfirmation.setAttribute('type', type);
        // Cambiar el icono del ojo
        this.classList.toggle('fa-eye-slash');
    });
</script>
@endsection