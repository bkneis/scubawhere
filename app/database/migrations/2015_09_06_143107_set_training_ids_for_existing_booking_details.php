<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class SetTrainingIdsForExistingBookingDetails extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		$bookingdetails = Bookingdetail::whereNotNull('training_session_id')->with('training_session')->get();

		$bookingdetails->each(function($detail)
		{
			$detail->training_id = $detail->training_session->training_id;
			$detail->save();
		});
	}

	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		DB::table('booking_details')->update(['training_id' => null]);
	}

}
