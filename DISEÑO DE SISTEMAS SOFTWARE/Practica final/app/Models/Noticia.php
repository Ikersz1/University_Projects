<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Noticia extends Model
{
    use HasFactory;

    protected $guarded = [];
    

    /**
     * Obtiene el equipo asociado a la noticia.
     */
    public function equipo()
    {
        return $this->belongsTo(Equipo::class);
    }
}

