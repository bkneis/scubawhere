<?php namespace ScubaWhere\Repositories;

use Illuminate\Support\ServiceProvider;
use ScubaWhere\Repositories\CreditRepo;

class CreditRepoServiceProvider extends ServiceProvider
{
	public function register()
	{
		$this->app->bind('ScubaWhere\Repositories\CreditRepoInterface', function($app)
		{
			return new CreditRepo();
		});
	}	
}
