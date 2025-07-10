<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\User;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Session;

class PerfilUsuarioController extends Controller
{
    public function index()
    {
        $user = Auth::user();
        return view('perfilUsuario.index', compact('user'));
    }

    public function edit()
    {
        $user = User::findOrFail(auth()->id());
        return view('perfilUsuario.edit', compact('user'));
    }

    // Modifica tu controlador PerfilUsuarioController@update
    public function update(Request $request, $id)
    {
        $user = User::findOrFail($id);
        //dd($request->file('profile_picture'));

        // Validación de campos
        $validator = Validator::make($request->all(), [
            'name' => 'sometimes|required|string|max:255',
            'email' => 'sometimes|required|email|unique:users,email,' . $id,
            'password' => 'nullable|string|min:8|confirmed',
            'password_confirmation' => 'nullable|string|same:password',
            'profile_picture' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048', // Validar imagen
        ]);

        // Redirecciona de vuelta con mensaje de éxito o errores de validación
        if ($validator->fails()) {
            return redirect()->back()->withErrors($validator)->withInput();
        }

        // Asigna los valores al modelo User si se proporcionan
        $user->name = $request->name;

        if ($request->password != null) {
            $user->password = bcrypt($request->password);
        }

        if ($request->hasFile('profile_picture')) {
            // Generar un nombre único para la imagen
            $imageName = 'profile_' . str_replace('@', '_', $user->email) . '.' . $request->file('profile_picture')->getClientOriginalExtension();

            // Guardar la imagen en public/images/users/
            $request->file('profile_picture')->move(public_path('images/users'), $imageName);

            // Guardar la ruta de la imagen en el modelo de usuario
            $user->profile_picture = 'images/users/' . $imageName;
        }

        // Guarda el usuario en la base de datos
        $user->save();
        Session::put('userName', Auth::user()->name);
        Auth::login($user);


        return redirect()->route('perfilUsuario.index')->with('success', 'Perfil actualizado correctamente');
    }
}
