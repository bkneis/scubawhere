<?php namespace Scubawhere\Repositories;

use Illuminate\Support\ServiceProvider;
use Scubawhere\Repositories\CreditRepo;

class CreditRepoServiceProvider extends ServiceProvider
{
	public function register()
	{
		$this->app->bind('Scubawhere\Repositories\CreditRepoInterface', function($app)
		{
			return new CreditRepo();
		});
	}	
}
