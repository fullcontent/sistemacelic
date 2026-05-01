<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateUserAccessesTable extends Migration
{
	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('user_accesses', function(Blueprint $table)
		{
			$table->increments('id');
			$table->integer('user_id')->unsigned()->index('user_accesses_user_id_foreign');
			$table->integer('empresa_id')->unsigned()->nullable()->index('user_accesses_empresa_id_foreign');
			$table->integer('unidade_id')->unsigned()->nullable()->index('user_accesses_unidade_id_foreign');
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
		Schema::drop('user_accesses');
	}
}
