<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('equipos', function (Blueprint $table) {
	    $table->id();
	    $table->string('nombre');
	    $table->foreignId('liga_id')->constrained()->onDelete('cascade');
	    $table->integer('ganados');
	    $table->integer('empatados');
	    $table->integer('perdidos');
	    $table->integer('goles_favor');
	    $table->integer('goles_contra');
	    $table->integer('puntos');
	    $table->integer('partidos_jugados');
	    $table->timestamps();
});
        
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('equipos');
    }
};
