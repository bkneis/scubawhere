<?php namespace ScubaWhere\Repositories;

use Illuminate\Support\ServiceProvider;
use ScubaWhere\Repositories\CustomerRepo;

class CustomerRepoServiceProvider extends ServiceProvider
{
	public function register()
	{
		$this->app->bind('ScubaWhere\Repositories\CustomerRepoInterface', function($app)
		{
			return new CustomerRepo();
		});
	}	
}
