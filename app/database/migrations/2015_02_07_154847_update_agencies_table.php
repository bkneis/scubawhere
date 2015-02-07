<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class UpdateAgenciesTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{

		try {
			Schema::table('companies', function($table)
			{
				$table->dropColumn('terms');
			});
		}
		catch(Exception $e) {}

		DB::table('agencies')->insert(
			array(
					array('abbreviation' => 'HSA'),
					array('abbreviation' => 'RAID'),
					array('abbreviation' => 'other'),
			)
		);

		Schema::table('companies', function($table)
		{
			$table->text('terms')->after('photo');
		});
	}

	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		DB::table('agencies')->where('abbreviation', '=', 'HSA')->delete();
		DB::table('agencies')->where('abbreviation', '=', 'RAID')->delete();
		DB::table('agencies')->where('abbreviation', '=', 'other')->delete();

		Schema::table('companies', function($table)
		{
			$table->dropColumn('terms');
		});
	}

}
