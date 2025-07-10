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
        Schema::create('equipo_titulo', function (Blueprint $table) {
        $table->foreignId('equipo_id')->constrained('equipos')->onDelete('cascade');
	    $table->foreignId('titulo_id')->constrained('titulos')->onDelete('cascade');
	    $table->timestamps();
	    
	    $table->primary(['equipo_id', 'titulo_id']);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('equipo_titulo');
    }
};
