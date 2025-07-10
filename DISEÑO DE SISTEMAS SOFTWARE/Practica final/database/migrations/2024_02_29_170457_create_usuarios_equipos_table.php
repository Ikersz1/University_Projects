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
        /*
        Schema::create('usuarios_equipos', function (Blueprint $table) {
        $table->foreignId('usuario_id')->constrained()->onDelete('cascade');
	    $table->foreignId('equipo_id')->constrained()->onDelete('cascade');
	    $table->primary(['usuario_id', 'equipo_id']);
	    }); */
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('usuarios_equipos');
    }
};
