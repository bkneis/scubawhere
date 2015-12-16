<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateCrmCustomerSubscription extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('crm_subscriptions', function($table){

			$table->engine = 'InnoDB';

			$table->increments('id');
            
            $table->integer('customer_id')->unsigned();

			$table->string('token', 128);
            
            $table->boolean('subscribed')->default(1);
            
            $table->integer('unsubscribed_campaign_id')->unsigned()->nullable();

			$table->timestamps();
            
            $table->foreign('customer_id')->references('id')->on('customers')->onUpdate('cascade')->onDelete('cascade');
            
            $table->foreign('unsubscribed_campaign_id')->references('id')->on('crm_campaigns')->onUpdate('cascade')->onDelete('cascade');

		});
	}

	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		Schema::drop('crm_subscriptions');
	}

}
