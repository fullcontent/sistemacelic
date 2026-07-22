<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateProjetoDossiesTable extends Migration
{
    public function up()
    {
        Schema::create('projeto_dossies', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('projeto_id')->unsigned(); // ID do projeto/serviço no celic
            $table->text('checklist_gerado')->nullable(); // O JSON ou Markdown gerado pela IA
            $table->text('historico_utilizado')->nullable(); // Quais pendências antigas foram usadas no prompt
            $table->string('status')->default('gerado');
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('projeto_dossies');
    }
}
