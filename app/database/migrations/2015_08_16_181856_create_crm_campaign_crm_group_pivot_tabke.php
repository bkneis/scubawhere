<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateCrmCampaignCrmGroupPivotTabke extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
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
		Schema::drop('crm_campaign_crm_group');
	}

}
