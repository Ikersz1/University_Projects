<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\Role;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Session; // Importa la clase Session
use Illuminate\Support\Facades\Validator;

class AdminUsuariosController extends Controller
{
    public function index(Request $request)
    {
        $query = $request->get('salida');
        $ordenNombre = $request->get('ordenNombre', 'asc');
        $usuariosQuery = User::query(); // Obtener la consulta base

        // Verificar si el query contiene el símbolo '@'
        if ($query !== null && strpos($query, '@') !== false) {
            $usuariosQuery->where('email', 'like', '%' . $query . '%');
        } else {
            // Si no contiene '@', buscar por nombre
            $usuariosQuery->where('name', 'like', $query . '%');
        }

        // Aplicar la ordenación después del filtro de búsqueda
        $usuarios = $usuariosQuery->orderBy('name', $ordenNombre)->paginate(10); // Paginación con 10 usuarios por página

        return view('admin.usuarios.index', compact('usuarios'));
    }

    public function create()
    {
        $roles = Role::all(); // Obtener todos los roles disponibles
        return view('admin.usuarios.create', compact('roles'));
    }

    public function store(Request $request)
    {
        // Validar los datos del formulario de registro
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users',
            'password' => 'required|string|min:8', 
            'password_confirmation' => 'required|string|same:password', // Verifica que la confirmación de la contraseña coincida con la contraseña
            'role_id' => 'required|exists:roles,id', // Validar el role_id
        ]);

        // Crear un nuevo usuario en la base de datos
        User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
            'role_id' => $request->role_id,
        ]);

        Session::flash('success', 'Usuario creado exitosamente.');
        return redirect()->route('admin.usuarios.index');
    }
    public function edit($id)
    {
        $usuario = User::findOrFail($id);
        $roles = Role::all(); // Obtener todos los roles disponibles
        return view('admin.usuarios.edit', compact('usuario', 'roles'));
    }

    public function update(Request $request, $id)
    {
        $usuario = User::findOrFail($id);

        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email,' . $id,
            'role_id' => 'required|exists:roles,id',
        ]);

        if ($validator->fails()) {
            return redirect()->back()->withErrors($validator)->withInput();
        }

        // Actualizar los campos del usuario con los datos del formulario
        $usuario->name = $request->name;
        $usuario->email = $request->email;
        $usuario->role_id = $request->role_id; // Actualizar el role_id
        $usuario->save();

        // Redirigir a la página de lista de usuarios con un mensaje de éxito
        return redirect()->route('admin.usuarios.index')->with('success', 'Usuario actualizado exitosamente.');
    }

    public function destroy($id)
    {
        $usuarioABorrar = User::findOrFail($id);
        $usuarioABorrar->delete();
        return redirect()->route('admin.usuarios.index')->with('success', 'Usuario eliminado correctamente.');
    }

    public function eliminarTodos()
    {
        // Eliminar todos los usuarios excepto los administradores
        User::whereHas('role', function ($query) {
            $query->whereNotIn('name', ['admin', 'noticiero', 'analista']);
        })->delete();

        return redirect()->route('admin.usuarios.index')->with('success', 'Todos los usuarios han sido eliminados.');
    }

    public function eliminarSeleccionados(Request $request)
    {
        // Obtener los IDs de usuarios seleccionados
        $usuariosSeleccionados = $request->input('usuarios_seleccionados', []);

        // Verificar si no se ha seleccionado ningún usuario
        if (empty($usuariosSeleccionados)) {
            // No hacer nada y redirigir con un mensaje de advertencia
            return redirect()->route('admin.usuarios.index')->with('warning', 'No se han seleccionado usuarios para eliminar.');
        }

        // Verificar que la entrada de usuarios seleccionados sea una cadena
        if (is_string($usuariosSeleccionados)) {
            // Convertir la cadena en un array de IDs de usuario
            $usuariosSeleccionados = explode(',', $usuariosSeleccionados);
        }

        // Convertir los IDs de usuario a enteros
        $usuariosSeleccionados = array_map('intval', $usuariosSeleccionados);

        // Eliminar los usuarios seleccionados
        User::whereIn('id', $usuariosSeleccionados)->delete();

        return redirect()->route('admin.usuarios.index')->with('success', 'Usuarios seleccionados eliminados correctamente.');
    }
}
