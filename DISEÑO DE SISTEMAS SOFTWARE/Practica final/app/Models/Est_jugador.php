<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Est_jugador extends Model
{
 	use HasFactory;	
    protected $guarded = [];
    
    //protected $table = 'est_jugadors';
    public function jugador()
    {
        return $this->belongsTo(Jugador::class);
    }
}
