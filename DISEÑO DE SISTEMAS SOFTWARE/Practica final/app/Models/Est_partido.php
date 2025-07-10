<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Est_partido extends Model
{
	use HasFactory;
    protected $guarded = [];
    //protected $table = 'est_partidos';
    public function partido()
    {
        
        return $this->belongsTo(Partido::class);
    }

}
