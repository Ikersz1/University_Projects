<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Response;
use App\Models\Thread; // Asegúrate de que el modelo Thread está correctamente importado

class ResponseController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth'); // Asegurar que solo usuarios autenticados pueden responder
    }


public function store(Request $request, $threadId)
{
    $request->validate([
        'content' => 'required|string'
    ]);

    $thread = Thread::findOrFail($threadId);

    $response = new Response([
        'thread_id' => $thread->id,
        'user_id' => auth()->id(),
        'content' => $request->input('content'),
        'parent_id' => $request->input('parent_id', null)
    ]);

    $response->save();

    return redirect()->route('threads.show', $thread->id)
        ->with('success', 'Respuesta publicada correctamente.');
}
public function destroy(Response $response)
{
    $this->authorize('delete', $response);

    $response->delete();

    return back()->with('success', 'Respuesta eliminada correctamente.');
}


}
