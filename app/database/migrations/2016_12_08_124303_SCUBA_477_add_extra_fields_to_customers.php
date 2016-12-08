<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class SCUBA477AddExtraFieldsToCustomers extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('languages', function (Blueprint $table) {
			$table->increments('id')->unsigned();
			$table->string('name');
			$table->string('abbreviation');
		});

		Schema::table('customers', function (Blueprint $table) {
			$table->integer('language_id')->unsigned()->nullable()->after('country_id');
			$table->foreign('language_id')->references('id')->on('languages')->onUpdate('cascade')->onDelete('cascade');
		});

		Schema::create('hotelstays', function (Blueprint $table) {
			$table->increments('id')->unsigned();
			$table->string('name');
			$table->string('address')->nullable();
			$table->date('arrival')->nullable();
			$table->date('departure')->nullable();
		});

		Schema::create('customer_hotelstay', function (Blueprint $table) {
			$table->integer('customer_id')->unsigned();
			$table->integer('hotelstay_id')->unsigned();
			$table->foreign('hotelstay_id')->references('id')->on('hotelstays')->onUpdate('cascade')->onDelete('cascade');
			$table->foreign('customer_id')->references('id')->on('customers')->onUpdate('cascade')->onDelete('cascade');
		});
	}

	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		Schema::drop('languages');
		Schema::table('customers', function (Blueprint $table) {
			$table->dropColumn('language_id');
		});
		Schema::drop('hotelstays');
		Schema::drop('customer_hotelstay');
	}

}
