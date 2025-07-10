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
    public function inicializarEstadisticas($stats = null)
{   
    $defaultStats = [
        'goles' => 0,
        'asistencias' => 0,
        'amarillas' => 0,
        'rojas' => 0,
    ];

    // Si se proporcionaron estadísticas, úsalas, si no, usa los valores por defecto.
    $statsToUse = $stats ?: $defaultStats;

    if (!$this->estadisticas()->exists()) {
        $this->estadisticas()->create($statsToUse);
    }
}


    // Observador para el evento de creación
    protected static function boot()
{
    parent::boot();

    static::created(function ($jugador) {
        // Solo inicializa estadísticas si no se han creado aún.
        if (!$jugador->estadisticas()->exists()) {
            $jugador->inicializarEstadisticas();
        }
    });
}

}
