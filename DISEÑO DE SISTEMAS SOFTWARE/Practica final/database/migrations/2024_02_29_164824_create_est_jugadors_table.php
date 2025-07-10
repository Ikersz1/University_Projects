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
        Schema::create('est_jugadors', function (Blueprint $table) {
            $table->id();
            $table->integer('goles');
            $table->integer('asistencias');
            $table->integer('amarillas');
            $table->integer('rojas');
            $table->foreignId('jugador_id')->constrained('jugadors')->onDelete('cascade');
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
        Schema::dropIfExists('est_jugadors');
    }
};
