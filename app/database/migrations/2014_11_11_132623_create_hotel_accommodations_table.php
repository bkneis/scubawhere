<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateHotelAccommodationsTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::table('boatrooms', function($table)
		{
			$table->dropForeign('accommodations_company_id_foreign');
			$table->dropIndex('accommodations_company_id_foreign');

			$table->foreign('company_id')->references('id')->on('companies')->onUpdate('cascade')->onDelete('cascade');
		});

		Schema::create('accommodations', function($table)
		{
			$table->engine = 'InnoDB';

			$table->increments('id');

			$table->integer('company_id')->unsigned();

			$table->string('name');
			$table->text('description');
			$table->integer('quantity');

			$table->timestamps();
			$table->softDeletes();

			$table->foreign('company_id')->references('id')->on('companies')->onUpdate('cascade')->onDelete('cascade');
		});

		Schema::table('booking_details', function($table)
		{
			$table->integer('accommodation_id')->unsigned()->after('packagefacade_id')->nullable()->default(null);

			$table->foreign('accommodation_id')->references('id')->on('accommodations')->onUpdate('cascade')->onDelete('restrict');
		});
	}

	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		Schema::table('booking_details', function($table)
		{
			$table->dropForeign('booking_details_accommodation_id_foreign');

			$table->dropColumn('accommodation_id');
		});

		Schema::drop('accommodations');

		DB::table('prices')->where('owner_type', 'Accommodation')->delete();
	}

}
