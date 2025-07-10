<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Liga extends Model
{
    protected $guarded = [];

    use HasFactory;
    public function equipos()
    {
        return $this->hasMany(Equipo::class);
    }
}
