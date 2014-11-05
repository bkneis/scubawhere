<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddRegisterDetailsToCompanies extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::table('companies', function($table)
		{
		    $table->string('business_email')->after('currency');
		    $table->string('business_phone')->after('business_email');
		    $table->string('vat_number')->after('business_phone');
		    $table->string('registration_number')->after('vat_number');

		});
	}

	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		Schema::table('companies', function($table)
		{
		    $table->dropColumn('business_email');
		    $table->dropColumn('business_phone');
		    $table->dropColumn('vat_number');
		    $table->dropColumn('registration_number');

		});
	}

}
