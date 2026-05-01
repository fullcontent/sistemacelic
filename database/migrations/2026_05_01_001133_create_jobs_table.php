<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateJobsTable extends Migration
{
	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('jobs', function(Blueprint $table)
		{
			$table->bigIncrements('id');
			$table->string('queue')->index();
			$table->longText('payload');
			$table->boolean('attempts');
			$table->integer('reserved_at')->unsigned()->nullable();
			$table->integer('available_at')->unsigned();
			$table->integer('created_at')->unsigned();
		});
	}

	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		Schema::drop('jobs');
	}
}
