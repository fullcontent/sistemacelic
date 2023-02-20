<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateReembolsoTaxasTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('reembolso_taxas', function (Blueprint $table) {
            
            $table->increments('id');
            $table->timestamps();
            
                
                $table->unsignedInteger('reembolso_id');
                $table->foreign('reembolso_id')->references('id')->on('reembolsos');

                $table->unsignedInteger('taxa_id');
                $table->foreign('taxa_id')->references('id')->on('taxas');

                
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('reembolso_taxas');
    }
}
