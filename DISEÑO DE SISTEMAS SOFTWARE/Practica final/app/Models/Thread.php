<?php



namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Thread extends Model
{
    use HasFactory;

    // Especifica los campos que pueden ser asignados masivamente
    protected $fillable = ['topic', 'user_id', 'content', 'image'];

    /**
     * Relación Uno a Muchos (inversa) con User
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Relación Uno a Muchos con Response
     */
    
    public function responses()
    {
        return $this->hasMany(Response::class);
    }
}
