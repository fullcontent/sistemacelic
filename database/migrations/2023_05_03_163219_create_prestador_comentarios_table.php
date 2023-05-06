<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreatePrestadorComentariosTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('prestador_comentarios', function (Blueprint $table) {
            $table->increments('id');
            $table->timestamps();

            $table->integer('prestador_id');
            $table->integer('ordemCompra_id');
            $table->integer('user_id');
            $table->integer('rating');
            $table->text('comentario');
            
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('prestador_comentarios');
    }
}
