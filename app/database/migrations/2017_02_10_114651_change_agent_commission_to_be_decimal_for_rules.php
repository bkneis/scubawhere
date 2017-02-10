<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class ChangeAgentCommissionToBeDecimalForRules extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::table('agent_commission_rules', function (Blueprint $table) {
		    $table->dropColumn('commission');
		});

		Schema::table('agent_commission_rules', function (Blueprint $table) {
			$table->decimal('commission', 10, 2)->after('owner_id')->nullable();
		});
	}

	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		Schema::table('agent_commission_rules', function (Blueprint $table) {
		    $table->dropColumn('commission');
		});
		
		Schema::table('agent_commission_rules', function (Blueprint $table) {
			$table->integer('commission')->after('owner_id')->nullable();
		});
	}

}
