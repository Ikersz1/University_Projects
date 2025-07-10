<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\HomeController; // Importa el HomeController

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Aquí es donde puedes registrar rutas web para tu aplicación. Estas
| rutas son cargadas por el RouteServiceProvider dentro de un grupo que
| contiene el grupo de middleware "web". ¡Ahora crea algo grandioso!
|
*/

Route::get('/', function () {
    return view('layouts.app');
});

Route::get('/jugadores', function () {
    return view('jugadores');
});