<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class ChangeOpenedAtToDatetime extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		DB::select(DB::raw('ALTER TABLE crm_link_trackers CHANGE COLUMN `opened_time` `opened_time` datetime;'));
		DB::select(DB::raw('ALTER TABLE crm_tokens CHANGE COLUMN `opened_time` `opened_time` datetime;'));
	}

	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		DB::select(DB::raw('ALTER TABLE crm_link_trackers CHANGE COLUMN `opened_time` `opened_time` bigint(20);'));
		DB::select(DB::raw('ALTER TABLE crm_tokens CHANGE COLUMN `opened_time` `opened_time` bigint(20);'));
	}

}
