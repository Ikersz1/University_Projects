<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class HomeController extends Controller
{
    // Método para responder a la ruta "/"
    public function index()
    {
        // Retorna la vista que hayas creado, ej. "home.blade.php"
        return view('layouts.app');
    } 
}
