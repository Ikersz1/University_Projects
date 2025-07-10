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
        Schema::create('noticias', function (Blueprint $table) {
	    $table->id();
	    $table->dateTime('fecha')->nullable(); // Fecha que puede ser null
	    $table->string('link_de_la_web')->nullable(); // Link de la web que puede ser null
	    $table->string('titulo'); // Título de la noticia
	    $table->string('enlace_de_la_foto')->nullable(); // Enlace de la foto que puede ser null
	    $table->text('contenido'); // Contenido de la noticia
	    $table->string('autor')->nullable(); // Autor de la noticia que puede ser null
	    $table->foreignId('equipo_id')->nullable()->constrained('equipos')->onDelete('set null'); // Relación con la tabla equipos
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
        Schema::dropIfExists('noticias');
    }
};
