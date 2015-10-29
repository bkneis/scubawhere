<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class TransferExistingPickUpsToNewPickUpsTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		$bookings = Booking::whereNotNull('pick_up_location')->get();

		$bookings->each(function($booking)
		{
			$data = [
				'booking_id' => $booking->id,
				'location'   => $booking->pick_up_location,
				'date'       => $booking->pick_up_date,
				'time'       => $booking->pick_up_time
			];

			print_r($data);

			$pick_up = new PickUp($data);
			if(!$pick_up->validate())
			{
				print_r($pick_up->errors()->all());
				die('Validation error!');
			}

			$pick_up->save();
		});
	}

	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		DB::table('pick_ups')->truncate();
	}

}
