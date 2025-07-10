<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;


class ContactController extends Controller
{
    // Método para mostrar el formulario de contacto
    public function show()
    {
        return view('contacto');
    }

    // Método para manejar el envío del mensaje
    public function enviarMensaje(Request $request)
    {
        // Validar los campos del formulario
        $request->validate([
            'nombre' => 'required|string|max:255',
            'correo' => 'required|email|max:255',
            'mensaje' => 'required|string|max:255',
        ]);

        // Obtener los datos del formulario
        $nombre = $request->input('nombre');
        $correo = $request->input('correo');
        $mensaje = $request->input('mensaje');

        // Construir el mensaje completo
        $mensajeCompleto = "Nombre: $nombre\nCorreo: $correo\nMensaje: $mensaje\n";

        // Guardar el mensaje en un archivo de texto
        Storage::disk('local')->append('mensajes.txt', $mensajeCompleto);

        // Redireccionar de vuelta a la página de contacto con un mensaje de éxito
        return redirect()->route('contacto')->with('success', '¡Mensaje enviado correctamente!');
    }

    public function verMensajes()
    {
        // Obtener el contenido del archivo de mensajes
        $contenido = Storage::disk('local')->get('mensajes.txt');

        // Convertir el contenido en un array dividiendo por saltos de línea
        $mensajes = explode("\n", $contenido);

        // Pasar los mensajes a la vista
        return view('admin.mensajes', compact('mensajes'));
    }

    public function limpiarMensajes()
    {
        // Eliminar todos los mensajes del archivo mensajes.txt
        Storage::disk('local')->put('mensajes.txt', '');

        // Redireccionar de vuelta a la página de mensajes con un mensaje de éxito
        return redirect()->route('mostrarMensajes')->with('success', '¡Mensajes limpiados correctamente!');
    }
}
