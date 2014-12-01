<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class FillTimezoneFieldOnCompaniesTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		$companies = Company::all();

		$companies->each(function($company)
		{
			$timezone = 'https://maps.googleapis.com/maps/api/timezone/xml?location='.$company->latitude.','.$company->longitude.'&timestamp='.time().'&key=AIzaSyDBX2LjGDdq2QlaGq0UJ9RcEHYdodJXCWk';
			$timezone = simplexml_load_file($timezone);

			$company->timezone = $timezone->time_zone_id;
			if( !$company->updateUniques() )
				print_r($company->errors()->all());
		});
	}

	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		DB::unprepared("UPDATE `companies` SET `timezone`='';");
	}

}
