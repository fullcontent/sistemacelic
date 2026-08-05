<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateServicoEquipeTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('servico_equipe', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('servico_id')->unsigned();
            $table->integer('user_id')->unsigned();
            $table->string('papel'); // 'coordenador', 'responsavel_tecnico', 'analista'
            $table->timestamp('data_vinculo')->useCurrent();
            $table->timestamp('data_desvinculo')->nullable();
            $table->boolean('ativo')->default(true);
            $table->timestamps();

            $table->foreign('servico_id')->references('id')->on('servicos')->onDelete('cascade');
            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
            $table->index(['servico_id', 'ativo']);
            $table->index(['user_id', 'ativo']);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('servico_equipe');
    }
}
