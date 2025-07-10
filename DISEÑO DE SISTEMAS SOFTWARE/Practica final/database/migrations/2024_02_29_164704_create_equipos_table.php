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
	    $table->foreignId('liga_id')->nullable()->default(1)->constrained()->onDelete('cascade');
        $table->integer('ganados')->nullable()->default(0);
	    $table->integer('empatados')->nullable()->default(0);
	    $table->integer('perdidos')->nullable()->default(0);
	    $table->integer('goles_favor')->nullable()->default(0);
	    $table->integer('goles_contra')->nullable()->default(0);
	    $table->integer('puntos')->nullable()->default(0);
	    $table->integer('partidos_jugados')->nullable()->default(0);
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
