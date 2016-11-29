<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddCardReferenceAndNotesToPaymentsAndRefunds extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::table('payments', function (Blueprint $table) {
			$table->integer('card_ref')->nullable()->after('received_at');
			$table->string('notes')->nullable()->after('card_ref');
		});
		Schema::table('refunds', function (Blueprint $table) {
			$table->integer('card_ref')->nullable()->after('received_at');
			$table->string('notes')->nullable()->after('card_ref');
		});
	}

	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		Schema::table('payments', function (Blueprint $table) {
			$table->dropColumn('card_ref');
			$table->dropColumn('notes');
		});

		Schema::table('refunds', function (Blueprint $table) {
			$table->dropColumn('card_ref');
			$table->dropColumn('notes');
		});
	}

}
