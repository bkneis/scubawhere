<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateCertificateCustomerPivotTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::table('customers', function($table)
		{
			$table->dropForeign('customers_certificate_id_foreign');

			$table->dropColumn('certificate_id');
		});

		Schema::create('certificate_customer', function($table)
		{
			$table->engine = "InnoDB";

			$table->integer('certificate_id')->unsigned();
			$table->integer('customer_id')->unsigned();

			$table->timestamps();

			$table->foreign('certificate_id')->references('id')->on('certificates')->onUpdate('cascade')->onDelete('restrict');
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
		Schema::drop('certificate_customer');

		Schema::table('customers', function($table)
		{
			$table->integer('certificate_id')->unsigned()->after('company_id');

			$table->foreign('certificate_id')->references('id')->on('certificates')->onUpdate('cascade')->onDelete('restrict');
		});
	}

}
