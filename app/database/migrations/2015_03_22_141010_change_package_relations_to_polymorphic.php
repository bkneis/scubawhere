<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class ChangePackageRelationsToPolymorphic extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('packageables', function($table)
		{
			$table->engine = 'InnoDB';

			$table->integer('package_id')->unsigned();
			$table->integer('packageable_id')->unsigned();
			$table->string('packageable_type');
			$table->integer('quantity')->default(1);

			$table->timestamps();

			$table->foreign('package_id')->references('id')->on('packages')->onUpdate('cascade')->onDelete('cascade');
		});

		// Copy all package-relations over to the new table
		$packages = Package::with('Tickets')->get();
		DB::statement('SET FOREIGN_KEY_CHECKS=0;');
		foreach($packages as $package)
		{
			foreach($package->tickets as $t)
			{
				DB::table('packageables')->insert([
					'package_id'       => $t->pivot->package_id,
					'packageable_id'   => $t->pivot->ticket_id,
					'packageable_type' => 'Ticket',
					'quantity'         => $t->pivot->quantity,
					'created_at'       => $t->pivot->created_at,
					'updated_at'       => $t->pivot->updated_at,
				]);
			}
		}
		DB::statement('SET FOREIGN_KEY_CHECKS=1;');

		Schema::drop('package_ticket');
	}

	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		Schema::create('package_ticket', function($table)
		{
			$table->engine = 'InnoDB';

			$table->integer('package_id')->unsigned();
			$table->integer('ticket_id')->unsigned();
			$table->integer('quantity');

			$table->timestamps();

			$table->foreign('package_id')->references('id')->on('packages')->onUpdate('cascade')->onDelete('cascade');
			$table->foreign('ticket_id')->references('id')->on('tickets')->onUpdate('cascade')->onDelete('restrict');
		});

		// Copy all package relations over to the new table
		$packages = Package::with('Tickets')->all();
		DB::statement('SET FOREIGN_KEY_CHECKS=0;');
		foreach($packages as $package)
		{
			foreach($package->tickets as $t)
			{
				DB::table('package_ticket')->insert([
					'package_id' => $t->pivot->package_id,
					'ticket_id'  => $t->pivot->packageable_id,
					'quantity'   => $t->pivot->quantity,
					'created_at' => $t->pivot->created_at,
					'updated_at' => $t->pivot->updated_at,
				]);
			}
		}
		DB::statement('SET FOREIGN_KEY_CHECKS=1;');

		Schema::drop('packageables');
	}

}
