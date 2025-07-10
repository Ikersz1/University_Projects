<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Partido extends Model
{
    protected $guarded = [];
    //protected $table = 'partidos';
    use HasFactory;
    public function equipoLocal()
    {
        return $this->belongsTo(Equipo::class, 'equipo_local_id');
    }

    public function equipoVisitante()
    {
        return $this->belongsTo(Equipo::class, 'equipo_visitante_id');
    }

    public function estadisticas()
    {
        return $this->hasOne(Est_partido::class);
    }

    public function predicciones(){
        return $this->hasMany(Prediccion::class);
    }
}
