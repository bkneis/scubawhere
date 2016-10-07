<?php 

namespace ScubaWhere\Repositories;

use Illuminate\Support\ServiceProvider;
use ScubaWhere\Repositories\LocationRepo;

class LocationRepoServiceProvider extends ServiceProvider
{
	public function register()
	{
		$this->app->bind('ScubaWhere\Repositories\LocationRepoInterface', function($app)
		{
			return new LocationRepo();
		});
	}	
}
