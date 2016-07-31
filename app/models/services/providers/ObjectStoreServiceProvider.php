<?php namespace ScubaWhere\Repositories;

use Illuminate\Support\ServiceProvider;
use ScubaWhere\Repositories\ObjectStoreRepo;

class ObjectStoreRepoServiceProvider extends ServiceProvider
{
	public function register()
	{
		$this->app->bind('ScubaWhere\Repositories\ObjectStoreRepoInterface', function($app)
		{
			return new ObjectStoreRepo();
		});
	}	
}
