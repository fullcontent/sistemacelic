<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateNormasTecnicasTable extends Migration
{
    public function up()
    {
        Schema::create('normas_tecnicas', function (Blueprint $table) {
            $table->increments('id');
            $table->string('orgao'); // ex: VISA, Bombeiros, Prefeitura
            $table->string('estado', 2)->nullable(); // SP, RJ, etc.
            $table->string('municipio')->nullable();
            $table->string('titulo');
            $table->string('link_original')->nullable();
            $table->string('arquivo_path')->nullable(); // PDF salvo localmente
            $table->date('data_publicacao')->nullable();
            $table->boolean('indexado_rag')->default(false); // Flag de ingestão vetorial
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('normas_tecnicas');
    }
}
