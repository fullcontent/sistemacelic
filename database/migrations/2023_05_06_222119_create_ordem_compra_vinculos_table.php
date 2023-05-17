<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateOrdemCompraVinculosTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('ordem_compra_vinculos', function (Blueprint $table) {
            $table->integer('ordemCompra_id');
            $table->integer('servico_id');
            $table->decimal('valor', 10, 2)->nullable();
            $table->boolean('reembolso')->nullable()->default(false);
            // Add any other columns you need here:
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
        Schema::dropIfExists('ordem_compra_vinculos');
    }
}
