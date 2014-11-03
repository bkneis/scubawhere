<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class ChangeDayAndMonthFieldsOnPricesTableIntoDateFields extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::table('prices', function($table)
		{
			$table->dropColumn( array('fromDay', 'fromMonth', 'untilDay', 'untilMonth') );

			$table->date('from')->after('currency');
			$table->date('until')->after('from')->nullable()->default(null);
		});
	}

	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		Schema::table('prices', function($table)
		{
			$table->dropColumn( array('from', 'until') );

			$table->tinyInteger('fromDay')->after('currency');
			$table->tinyInteger('fromMonth')->after('fromDay');
			$table->tinyInteger('untilDay')->after('fromMonth');
			$table->tinyInteger('untilMonth')->after('untilDay');
		});
	}

}
