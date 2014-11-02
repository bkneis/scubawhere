<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreatePricesForTicketsAndPackages extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		$tickets = Ticket::all();
		$array = array();
		foreach($tickets as $ticket)
		{
			$array[] = array(
				'owner_id' => $ticket->id,
				'owner_type' => 'Ticket',
				'price' => 0,
				'currency' => 'GBP',
				'fromDay' => 1,
				'fromMonth' => 1,
				'untilDay' => 31,
				'untilMonth' => 12,
				'created_at' => date("Y-m-d H:i:s"),
				'updated_at' => date("Y-m-d H:i:s")
			);
		}
		DB::table('prices')->insert($array);


		$packages = Package::all();
		$array = array();
		foreach($packages as $package)
		{
			$array[] = array(
				'owner_id' => $package->id,
				'owner_type' => 'Package',
				'price' => 0,
				'currency' => 'GBP',
				'fromDay' => 1,
				'fromMonth' => 1,
				'untilDay' => 31,
				'untilMonth' => 12,
				'created_at' => date("Y-m-d H:i:s"),
				'updated_at' => date("Y-m-d H:i:s")
			);
		}
		DB::table('prices')->insert($array);
	}

	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		DB::table('prices')->truncate();
	}

}
