<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Usuario extends Model
{
    protected $guarded = [];
    //protected $table = 'usuarios';
    use HasFactory;
    public function equipos()
    {
        return $this->belongsToMany(Equipo::class, 'usuarios_equipos');
    }
}
