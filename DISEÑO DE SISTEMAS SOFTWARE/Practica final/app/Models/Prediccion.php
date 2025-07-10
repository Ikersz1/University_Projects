<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Prediccion extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'partido_id',
        'voto_local',
        'voto_empate',
        'voto_visitante',
    ];

    public function users()
    {
        return $this->belongsTo(User::class);
    }

    public function partidos()
    {
        return $this->belongsTo(Partido::class);
    }
}
