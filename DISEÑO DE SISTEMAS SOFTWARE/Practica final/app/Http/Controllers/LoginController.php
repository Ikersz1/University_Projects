<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Session;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Log;

class LoginController extends Controller
{
    public function showLoginForm()
    {
        // Guarda la URL anterior en la sesión
        session(['url_anterior' => url()->previous()]);

        return view('login.login');
    }
    public function login(Request $request)
    {
        // Validar los datos del formulario de inicio de sesión
        $credentials = $request->only('email', 'password');

        // Intentar autenticar al usuario
        if (Auth::attempt($credentials)) {
            // Guardar el nombre del usuario en la sesión
            Session::put('userName', Auth::user()->name);
            
            /*if(session('url_anterior') == null){
                // Guardar la URL anterior en la sesión
                Session::put('url_anterior', 'home');
            }*/

            // Redirigir a la URL anterior o a la ruta raíz
            return redirect()->route('home');
        } else {
            // Si la autenticación falla, redirigir de nuevo al formulario de inicio de sesión con un mensaje de error
            return redirect()->route('login')->with('error', 'Credenciales incorrectas. Por favor, inténtalo de nuevo.');
        }
    }
}
