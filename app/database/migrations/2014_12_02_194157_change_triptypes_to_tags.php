<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class ChangeTriptypesToTags extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		// First off, remove old pivot table
		Schema::table('trip_triptype', function($table)
		{
			$table->dropForeign('trip_triptype_trip_id_foreign');
			$table->dropForeign('trip_triptype_triptype_id_foreign');
		});
		Schema::drop('trip_triptype');

		// Rename model table
		Schema::rename('triptypes', 'tags');

		// Add new column to tags table to distinguish between the models that the tags are meant for
		Schema::table('tags', function($table)
		{
			$table->string('for_type', 128)->after('description');
		});

		// Create new pivot table
		Schema::create('taggables', function($table)
		{
			$table->engine = 'InnoDB';

			$table->integer('tag_id')->unsigned();
			$table->integer('taggable_id')->unsigned();
			$table->string('taggable_type', 128);

			$table->timestamps();
		});

		// Reseed tags table
		Artisan::call('db:seed', array('--class' => 'TagsTableSeeder'));

		Schema::table('taggables', function($table)
		{
			$table->foreign('tag_id')->references('id')->on('tags')->onUpdate('cascade')->onDelete('cascade');
		});
	}

	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		Schema::table('taggables', function($table)
		{
			$table->dropForeign('taggables_tag_id_foreign');
		});
		Schema::drop('taggables');

		Schema::rename('tags', 'triptypes');

		Schema::table('triptypes', function($table)
		{
			$table->dropColumn('for_type');
		});

		// Create new pivot table
		Schema::create('trip_triptype', function($table)
		{
			$table->engine = 'InnoDB';

			$table->integer('trip_id')->unsigned();
			$table->integer('triptype_id')->unsigned();

			$table->timestamps();

			$table->foreign('trip_id')->references('id')->on('trips')->onUpdate('cascade')->onDelete('cascade');
			$table->foreign('triptype_id')->references('id')->on('triptypes')->onUpdate('cascade')->onDelete('restrict');
		});
	}

}
