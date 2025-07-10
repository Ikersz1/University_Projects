<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Titulo extends Model
{
    protected $guarded = [];
    use HasFactory;
    public function equipos()
    {
        return $this->belongsToMany(Equipo::class, 'equipo_titulo');
    }
}
