<?php 

namespace Scubawhere\Repositories;

use Illuminate\Support\ServiceProvider;

class ObjectStoreRepoServiceProvider extends ServiceProvider
{
	public function register()
	{
		$this->app->bind('Scubawhere\Repositories\ObjectStoreRepoInterface', function($app)
		{
			return new ObjectStoreRepo();
		});
	}	
}
