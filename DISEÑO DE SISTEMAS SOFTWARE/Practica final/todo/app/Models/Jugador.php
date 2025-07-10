<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Jugador extends Model
{
    protected $guarded = [];
    //protected $table = 'jugadors';
    use HasFactory;
    public function equipo()
    {
        return $this->belongsTo(Equipo::class);
    }

    public function estadisticas()
    {
        return $this->hasOne(Est_jugador::class);
    }
}
