<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateCrmTables extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('crm_groups', function($table){

			$table->engine = 'InnoDB';

			$table->increments('id');

			$table->integer('company_id')->unsigned();

			$table->string('name', 128);

			$table->longText('description');

			$table->timestamps();

			$table->foreign('company_id')->references('id')->on('companies')->onUpdate('cascade')->onDelete('cascade');

		});

		Schema::create('crm_group_rules', function($table){

			$table->engine = 'InnoDB';

			$table->increments('id');

			$table->integer('crm_group_id')->unsigned();

			$table->integer('agency_id')->unsigned()->nullable()->default(null);

			$table->integer('certificate_id')->unsigned()->nullable()->default(null);

			$table->integer('ticket_id')->unsigned()->nullable()->default(null);

			$table->integer('training_id')->unsigned()->nullable()->default(null);

			$table->timestamps();

			$table->foreign('crm_group_id')->references('id')->on('crm_groups')->onUpdate('cascade')->onDelete('cascade');
			$table->foreign('certificate_id')->references('id')->on('certificates')->onUpdate('cascade')->onDelete('cascade');
			$table->foreign('agency_id')->references('id')->on('agencies')->onUpdate('cascade')->onDelete('cascade');
			$table->foreign('ticket_id')->references('id')->on('tickets')->onUpdate('cascade')->onDelete('cascade');
			$table->foreign('training_id')->references('id')->on('trainings')->onUpdate('cascade')->onDelete('cascade');

		});

		Schema::create('crm_campaigns', function($table){

			$table->engine = 'InnoDB';

			$table->increments('id');

			$table->integer('company_id')->unsigned();

			$table->longText('subject');

			$table->longText('message');

			$table->dateTime('sent_at');

			$table->integer('num_sent');

			$table->timestamps();

			$table->foreign('company_id')->references('id')->on('companies')->onUpdate('cascade')->onDelete('cascade');

		});

		Schema::create('crm_campaign_crm_group', function($table) {

			$table->engine = 'InnoDB';

			$table->integer('crm_campaign_id')->unsigned();

			$table->integer('crm_group_id')->unsigned();

			$table->foreign('crm_campaign_id')->references('id')->on('crm_campaigns')->onUpdate('cascade')->onDelete('cascade');

			$table->foreign('crm_group_id')->references('id')->on('crm_groups')->onUpdate('cascade')->onDelete('cascade');

		});
	}

	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		Schema::drop('crm_groups');
		Schema::drop('crm_campaigns');
        Schema::drop('crm_group_rules');
		Schema::drop('crm_campaign_crm_group');
	}

}
