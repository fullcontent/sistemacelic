<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateSolicitantesTable extends Migration
{
	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('solicitantes', function(Blueprint $table)
		{
			$table->increments('id');
			$table->string('nome')->nullable();
			$table->string('email')->nullable();
			$table->string('telefone')->nullable();
			$table->string('departamento')->nullable();
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
		Schema::drop('solicitantes');
	}
}
