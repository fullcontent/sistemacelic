<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateComprovantesTable extends Migration
{
    public function up()
    {
        Schema::create('comprovantes', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('boleto_id')->unsigned()->nullable();
            $table->decimal('valor_pago', 10, 2)->nullable();
            $table->string('favorecido_pago')->nullable();
            $table->date('data_pagamento')->nullable();
            $table->string('arquivo_path')->nullable();
            $table->string('status_auditoria')->default('pendente'); // pendente, extraido, erro
            $table->boolean('divergencia')->default(false); // Flag se houve divergência com o boleto
            $table->text('motivo_divergencia')->nullable(); // IA explica a divergência
            $table->boolean('reembolso_bloqueado')->default(false); // Regra "Diferente de Castro"
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('comprovantes');
    }
}
