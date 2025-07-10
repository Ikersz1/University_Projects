
<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\HomeController; // Importa el HomeController
use App\Http\Controllers\RegisterController;
use App\Http\Controllers\LoginController;
use App\Http\Controllers\JugadoresController;
use App\Http\Controllers\NoticiasController;
use App\Http\Controllers\CalendarioController;
use App\Http\Controllers\ClasificacionController;
use App\Http\Controllers\FavoritosController;
use App\Http\Controllers\JugadorController;
use App\Http\Controllers\AdminController;
use App\Http\Controllers\AdminJugadoresController;
use App\Http\Controllers\AdminUsuariosController;
use App\Http\Controllers\AdminPartidoController;
use App\Http\Controllers\AdminNoticiasController;
use App\Http\Controllers\EquipoController;
use App\Http\Controllers\AdminEquipoController;
use App\Http\Controllers\LogoutController;
use App\Http\Controllers\ResponseController;
use App\Http\Controllers\PerfilUsuarioController;
use App\Http\Controllers\ThreadController;

Route::get('/', [HomeController::class, 'index'])->name('home');
Route::get('/noticias', [NoticiasController::class, 'index'])->name('noticias');
Route::get('/noticias/{id}', [NoticiasController::class, 'getDatos'])->name('noticias.show');
Route::get('/noticias/equipo/{id}', [NoticiasController::class, 'getEquipoName'])->name('noticias.getEquipoName');



Route::get('/calendario/{jornada?}', [CalendarioController::class, 'index'])->name('calendario.index');
Route::get('/calendario/{equipo}/show', [CalendarioController::class, 'show'])->name('calendario.show');
Route::get('/partidos/{id}', [CalendarioController::class, 'showEstadisticas'])->name('partidos');
Route::post('/guardar-voto', [CalendarioController::class, 'guardarVoto'])->name('guardarVoto');
Route::get('/clasificacion', [ClasificacionController::class, 'index'])->name('clasificacion');
Route::get('/favoritos', [FavoritosController::class, 'index'])->name('favoritos');

Route::get('/register', [RegisterController::class, 'showRegistrationForm'])->name('register');
Route::post('/register', [RegisterController::class, 'register']);
Route::get('/login', [LoginController::class, 'showLoginForm'])->name('login');
Route::post('/login', [LoginController::class, 'login']);


Route::get('/favoritos', [FavoritosController::class, 'index'])->name('favoritos.index');
Route::get('/favoritos/{nombreEquipo}/edit', [FavoritosController::class, 'editar'])->name('favoritos.edit');
Route::delete('/favoritos/{nombreEquipo}', [FavoritosController::class, 'delete'])->name('favoritos.delete');

Route::get('/confirmar-cerrar-sesion', [LogoutController::class, 'confirmarCerrarSesion'])->name('confirmar.cerrar.sesion');
Route::post('/logout', [LogoutController::class, 'logout'])->name('logout');

Route::get('/perfilUsuario', [PerfilUsuarioController::class, 'index'])->name('perfilUsuario.index');
Route::get('/perfilUsuario/edit', [PerfilUsuarioController::class, 'edit'])->name('perfilUsuario.edit');
Route::put('/{id}/update', [PerfilUsuarioController::class, 'update'])->name('perfilUsuario.update');



Route::get('/contacto', 'App\Http\Controllers\ContactController@show')->name('contacto');
Route::post('/contacto/enviar', 'App\Http\Controllers\ContactController@enviarMensaje')->name('enviarMensaje');
Route::get('/admin/mensajes', 'App\Http\Controllers\ContactController@verMensajes')->name('admin.mensajes');
Route::post('/limpiar-mensajes', 'App\Http\Controllers\ContactController@limpiarMensajes')->name('limpiarMensajes');
Route::get('/mostrar-mensajes', 'App\Http\Controllers\ContactController@verMensajes')->name('mostrarMensajes');


Route::get('/jugadores', [JugadoresController::class, 'index'])->name('jugadores');
Route::get('/jugadores/{id}', [JugadoresController::class, 'show'])->name('jugadores.show');

Route::get('/threads/search', [ThreadController::class, 'search']);


// Rutas para hilos
Route::get('/threads', [ThreadController::class, 'index'])->name('threads.index');
Route::get('/threads/create', [ThreadController::class, 'create'])->name('threads.create')->middleware('auth');
Route::post('/threads', [ThreadController::class, 'store'])->name('threads.store')->middleware('auth');
Route::get('/threads/{thread}', [ThreadController::class, 'show'])->name('threads.show');

Route::delete('threads/{thread}', [ThreadController::class, 'destroy'])->name('threads.destroy');
Route::get('/threads/users', [ThreadController::class, 'threadsByUser'])->name('threads.users');

Route::get('/toggleThreads', [ThreadController::class,'toggleThreads'])->name('threads.toggle');
// Rutas para respuestas
Route::post('/threads/{thread}/responses', [ResponseController::class, 'store'])->name('responses.store');
Route::delete('responses/{response}', [ResponseController::class, 'destroy'])->name('responses.destroy');

//rutas de admin
Route::group(['middleware' => ['role:admin,analista,noticiero']], function () {
    Route::get('/admin', [AdminController::class, 'index'])->name('admin.index');
    Route::post('/admin', [AdminController::class, 'store'])->name('admin.store');
});
Route::group(['middleware' => ['role:admin']], function () {
   

    // Administración de usuarios
    Route::prefix('/admin/usuarios')->group(function () {
        Route::get('/', [AdminUsuariosController::class, 'index'])->name('admin.usuarios.index');
        Route::get('/create', [AdminUsuariosController::class, 'create'])->name('admin.usuarios.create');
        Route::post('/store', [AdminUsuariosController::class, 'store'])->name('admin.usuarios.store');
        Route::get('/{id}/edit', [AdminUsuariosController::class, 'edit'])->name('admin.usuarios.edit');
        Route::put('/{id}/update', [AdminUsuariosController::class, 'update'])->name('admin.usuarios.update');
        Route::delete('/{id}', [AdminUsuariosController::class, 'destroy'])->name('admin.usuarios.destroy');
        Route::post('/usuarios/eliminar-todos', [AdminUsuariosController::class, 'eliminarTodos'])->name('admin.usuarios.eliminar-todos');
        Route::post('/usuarios/eliminar-seleccionados', [AdminUsuariosController::class, 'eliminarSeleccionados'])->name('admin.usuarios.eliminar-seleccionados');
    });
});
    Route::group(['middleware' => ['role:admin,analista']], function () {

        // Administración de jugadores
    Route::prefix('/adminjugadores')->group(function () {
        Route::get('/', [AdminJugadoresController::class, 'index'])->name('admin.adminjugador');
        Route::post('/eliminar/{id}', [AdminJugadoresController::class, 'eliminar']);
        Route::post('/crear', [AdminJugadoresController::class, 'crear'])->name('adminjugador.crear');
        Route::get('/datos/{id}', [AdminJugadoresController::class, 'getDatos'])->name('jugadores.getDatos');
        Route::put('/adminjugadores/actualizar/{id}', [AdminJugadoresController::class, 'actualizar'])->name('jugadores.actualizar');
        Route::get('/jugadores/editar/{id}', [AdminJugadoresController::class, 'editar'])->name('jugadores.editar');
        Route::post('/eliminar-masa', [AdminJugadoresController::class, 'eliminarMasa'])->name('adminjugadores.eliminar-masa');
        Route::post('/eliminar-todos', [AdminJugadoresController::class, 'eliminarTodos'])->name('adminjugadores.eliminar-todos');
        Route::post('/insertar-jugadores', [AdminJugadoresController::class, 'insertarJugadores'])->name('adminjugadores.insertar-jugadores');

        });

    // Administración de partidos
    Route::prefix('/admin/partidos')->group(function () {
        Route::get('/', [AdminPartidoController::class, 'index'])->name('admin.partidos.index');
        Route::get('/create', [AdminPartidoController::class, 'create'])->name('admin.partidos.create');
        Route::post('/store', [AdminPartidoController::class, 'store'])->name('admin.partidos.store');
        Route::get('/{id}/edit', [AdminPartidoController::class, 'edit'])->name('admin.partidos.edit');
        Route::get('/{equipo}/show', [AdminPartidoController::class, 'show'])->name('admin.partidos.show');
        Route::put('/{id}/update', [AdminPartidoController::class, 'update'])->name('admin.partidos.update');
        Route::delete('/{id}/delete', [AdminPartidoController::class, 'delete'])->name('admin.partidos.delete');
        Route::delete('/deleteSelected', [AdminPartidoController::class, 'deleteSelected'])->name('admin.partidos.deleteSelected');
        Route::delete('/deleteAll', [AdminPartidoController::class, 'deleteAll'])->name('admin.partidos.deleteAll');
    });
    // Administración de equipos
    Route::prefix('/adminequipos')->group(function () {
        Route::get('/', [AdminEquipoController::class, 'index'])->name('admin.equipos.index');
        Route::post('/crear', [AdminEquipoController::class, 'crear'])->name('admin.equipos.crear');
        Route::get('/datos/{id}', [AdminEquipoController::class, 'getDatos'])->name('equipos.getDatos');
        Route::delete('/eliminar/{id}', [AdminEquipoController::class, 'eliminar'])->name('admin.equipos.eliminar');
        Route::post('/actualizar/{id}', [AdminEquipoController::class, 'actualizar'])->name('equipos.actualizar');
    });
});

    
    

    // Administración de noticias
    Route::group(['middleware' => ['role:admin,noticiero']], function () {
    Route::prefix('/adminnoticias')->group(function () {
        Route::get('/', [AdminNoticiasController::class, 'index'])->name('admin.noticias.index');
        Route::post('/crear', [AdminNoticiasController::class, 'crear'])->name('admin.noticias.crear');
        Route::delete('/eliminar-todas', [AdminNoticiasController::class, 'eliminarTodas'])->name('admin.noticias.eliminar-todas');
        Route::get('/datos/{id}', [AdminNoticiasController::class, 'getDatos'])->name('noticias.getDatos');
        Route::delete('/eliminar/{id}', [AdminNoticiasController::class, 'eliminar'])->name('admin.noticias.eliminar');
        Route::post('/actualizar/{id}', [AdminNoticiasController::class, 'actualizar'])->name('noticias.actualizar');
        Route::get('/equipo/{id}', [AdminNoticiasController::class, 'getEquipoName']);
        Route::post('/eliminar-masa', [AdminNoticiasController::class, 'eliminarMasa']);
    });
});

    
    


// Rutas para EquipoController
Route::get('/clasificacion', [ClasificacionController::class, 'index'])->name('clasificacion');
Route::get('/equipos', [EquipoController::class, 'index'])->name('equipos.index');
Route::get('/equipos/{equipo}', [EquipoController::class, 'show'])->name('equipos.show');
Route::post('/equipos/{equipo}/favorito', [EquipoController::class, 'agregarFavorito'])->name('equipos.agregarFavorito');
Route::delete('/equipos/{equipo}/favorito', [EquipoController::class, 'eliminarFavorito'])->name('equipos.eliminarFavorito');

//Rutas noticias


// Rutas traducción
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Redirect;

Route::get('/lang/{lang}', function ($lang) {
    if (in_array($lang, ['en', 'es'])) {
        Session::put('applocale', $lang);
    }
    return Redirect::back();
});
