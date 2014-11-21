<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class EmptyAccommodationsTableAndDeleteAccommodationPrices extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Accommodation::all()->each(function($e)
		{
			$e->delete();
		});
		Price::where(Price::$owner_type_column_name, 'Accommodation')->delete();
	}

	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		// Not really reversible...
	}

}
