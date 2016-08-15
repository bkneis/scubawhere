<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateLogEntriesTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
        Schema::create('log_entries', function($table)
        {
            $table->increments('id');
            $table->integer('log_id')->unsigned();
            $table->string('description');
            $table->timestamps();
            $table->foreign('log_id')->references('id')->on('logs')->onUpdate('cascade')->onDelete('cascade');
        });
	}

	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
        Schema::drop('log_entries');
	}

}
