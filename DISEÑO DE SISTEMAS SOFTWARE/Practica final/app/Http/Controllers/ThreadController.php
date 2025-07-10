<?php

namespace App\Http\Controllers;

use App\Models\Thread;
use Illuminate\Http\Request;
use App\Models\User;
use Illuminate\Support\Facades\Log;

class ThreadController extends Controller
{
    
    public function index()
{
    $threads = Thread::with('user')->get(); // Carga anticipada de usuarios
    return view('threads.index', compact('threads'));
}
public function search(Request $request)
{
    $query = Thread::query();

    if ($request->has('search')) {
        $search = $request->input('search');
        $query->where('topic', 'like', "%{$search}%")
              ->orWhereHas('user', function ($q) use ($search) {
                  $q->where('name', 'like', "%{$search}%");
              });
    }

    if ($request->input('showMy') === 'true' && auth()->check()) {
        $query->where('user_id', auth()->id());
    }

    $threads = $query->get();

    return view('threads.thread_list', ['threads' => $threads]);
}


public function toggleThreads(Request $request)
{
    if (!auth()->check()) {
        // Retorna un estado HTTP 401 con un mensaje, en lugar de redireccionar
        return response()->json(['error' => 'Unauthorized'], 401);
    }

    $threads = Thread::all();

    if ($request->query('showMy', 'false') === 'true') {
        $threads = Thread::where('user_id', auth()->id())->get();
    }

    // Devuelve la vista como HTML
    return view('threads.thread_list', ['threads' => $threads])->render();
}




public function threadsByUser()
{   
    Log::info('Accediendo a threadsByUser');
    $users = User::with('threads')->get();
    return view('threads.users', compact('users'));

}








    
    public function create()
    {
        return view('threads.create'); // Devolver la vista para crear un nuevo hilo
    }

    
    public function store(Request $request)
{
    $request->validate([
        'topic' => 'required|string|max:255',
        'content' => 'required|string',
        'image' => 'nullable|image'
    ]);

    $thread = new Thread($request->all());
    $thread->user_id = auth()->id(); // Asegúrate de que el usuario esté autenticado
    if ($request->hasFile('image') && $request->file('image')->isValid()) {
        $thread->image = $request->image->store('threads_images', 'public');
    }
    $thread->save();

    return redirect()->route('threads.index')->with('success', 'Hilo creado exitosamente!');
}


    
    public function show($id)
{
    $thread = Thread::with(['responses' => function ($query) {
        $query->whereNull('parent_id')
              ->with(['children' => function ($query) {
                  $query->with('user'); // Carga de forma recursiva
              }, 'user']);
    }, 'user'])->findOrFail($id);

    return view('threads.show', compact('thread'));
}


   

    

    
    public function destroy(Thread $thread)
{
    $this->authorize('delete', $thread);

    $thread->delete();

    return redirect()->route('threads.index')
        ->with('success', 'Hilo eliminado correctamente.');
}

}
