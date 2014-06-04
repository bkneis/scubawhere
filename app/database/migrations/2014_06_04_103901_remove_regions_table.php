<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class RemoveRegionsTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::table('companies', function($table)
		{
			$table->dropForeign('companies_region_id_foreign');
			$table->dropColumn('region_id');
		});

		Schema::table('customers', function($table)
		{
			$table->dropForeign('customers_region_id_foreign');
			$table->dropColumn('region_id');
		});

		Schema::drop('regions');
	}

	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		Schema::create('regions', function($table)
		{
			$table->increments('id');
			$table->integer('country_id')->unsigned();
			$table->string('abbreviation', 8);
			$table->string('name', 128);
			$table->text('description');
			$table->timestamps();

			$table->foreign('country_id')->references('id')->on('countries')->onUpdate('cascade')->onDelete('cascade');
		});

		Schema::table('companies', function($table)
		{
			$table->integer('region_id')->unsigned()->after('postcode');

			$table->foreign('region_id')->references('id')->on('regions')->onUpdate('cascade')->onDelete('restrict');
		});

		Schema::table('customers', function($table)
		{
			$table->integer('region_id')->unsigned()->after('postcode');

			$table->foreign('region_id')->references('id')->on('regions')->onUpdate('cascade')->onDelete('restrict');
		});
	}

}
