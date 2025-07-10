<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Equipo extends Model
{
    protected $guarded = [];
     use HasFactory;

    public function liga()
    {
        return $this->belongsTo(Liga::class);
    }

    public function jugadores()
    {
        return $this->hasMany(Jugador::class);
    }

    public function partidosComoLocal()
    {
        return $this->hasMany(Partido::class, 'equipo_local_id');
    }

    public function partidosComoVisitante()
    {
        return $this->hasMany(Partido::class, 'equipo_visitante_id');
    }

    public function users()
    {
        return $this->belongsToMany(User::class, 'users_equipos');
    }

    public function usuarios()
    {
        return $this->belongsToMany(User::class, 'usuarios_equipos');
    }

    public function titulos()
    {
        return $this->belongsToMany(Titulo::class, 'equipo_titulo');
    }
    public function noticias()
    {
        return $this->hasMany(Noticia::class);
    }
}
