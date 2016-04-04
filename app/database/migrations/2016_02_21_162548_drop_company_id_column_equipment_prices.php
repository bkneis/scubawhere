<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class DropCompanyIdColumnEquipmentPrices extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::table('equipment_prices', function($table) {
            $table->dropForeign('equipment_prices_company_id_foreign'); // DROP THE FOREIGN KEY FIRST!!
            $table->dropColumn('company_id');
        });
	}

	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		Schema::table('equipment_prices', function($table) {
            $table->integer('company_id')->unsigned();
            $table->foreign('company_id')->references('id')->on('companies')->onUpdate('cascade')->onDelete('cascade');
        });
	}

}