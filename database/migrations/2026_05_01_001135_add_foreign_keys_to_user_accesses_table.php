<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddForeignKeysToUserAccessesTable extends Migration
{
	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::table('user_accesses', function(Blueprint $table)
		{
			$table->foreign('empresa_id')->references('id')->on('empresas')->onUpdate('NO ACTION')->onDelete('NO ACTION');
			$table->foreign('unidade_id')->references('id')->on('unidades')->onUpdate('NO ACTION')->onDelete('NO ACTION');
			$table->foreign('user_id')->references('id')->on('users')->onUpdate('NO ACTION')->onDelete('NO ACTION');
		});
	}

	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		Schema::table('user_accesses', function(Blueprint $table)
		{
			$table->dropForeign('user_accesses_empresa_id_foreign');
			$table->dropForeign('user_accesses_unidade_id_foreign');
			$table->dropForeign('user_accesses_user_id_foreign');
		});
	}
}
