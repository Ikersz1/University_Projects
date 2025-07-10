<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Response extends Model
{
    use HasFactory;

    // Especifica los campos que pueden ser asignados masivamente
    protected $fillable = ['thread_id', 'user_id', 'content'];

    /**
     * Relación Uno a Muchos (inversa) con Thread
     */
    public function thread()
    {
        return $this->belongsTo(Thread::class);
    }

    /**
     * Relación Uno a Muchos (inversa) con User
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }
    public function parent()
    {
        return $this->belongsTo(Response::class, 'parent_id');
    }

    public function children()
    {
        return $this->hasMany(Response::class, 'parent_id');
    }
}
