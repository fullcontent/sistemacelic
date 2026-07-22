<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateBoletosTable extends Migration
{
    public function up()
    {
        Schema::create('boletos', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('faturamento_id')->unsigned()->nullable();
            $table->decimal('valor', 10, 2)->nullable();
            $table->string('favorecido')->nullable();
            $table->string('linha_digitavel')->nullable();
            $table->date('vencimento')->nullable();
            $table->string('arquivo_path')->nullable(); // Caminho no Storage
            $table->string('status_auditoria')->default('pendente'); // pendente, extraido, erro
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('boletos');
    }
}
