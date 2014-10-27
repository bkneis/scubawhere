<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class SetCompanyLocationToProDiveCairns extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		DB::table('companies')->where('id', 1)->update( array('latitude' => -16.922882, 'longitude' => 145.774445) );
	}

	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		DB::table('companies')->where('id', 1)->update( array('latitude' => 0, 'longitude' => 0) );
	}

}
