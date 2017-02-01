<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class ChangePolymorphicColumnNameForCommissionRules extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::table('agent_commission_rules', function (Blueprint $table) {
		    $table->renameColumn('type', 'owner_type');
			$table->renameColumn('item_id', 'owner_id');
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
			$table->renameColumn('owner_type', 'type');
			$table->renameColumn('owner_id', 'item_id');
		});
	}

}
