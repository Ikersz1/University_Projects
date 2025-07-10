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
        Schema::create('est_partidos', function (Blueprint $table) {
             $table->id();
	    $table->integer('goles_local');
	    $table->integer('goles_visitante');
	    $table->integer('amarillas');
	    $table->integer('rojas');
	    $table->foreignId('partido_id')->constrained()->onDelete('cascade');
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
        Schema::dropIfExists('est_partidos');
    }
};
