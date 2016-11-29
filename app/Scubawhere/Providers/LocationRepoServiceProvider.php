<?php 

namespace Scubawhere\Repositories;

use Illuminate\Support\ServiceProvider;
use Scubawhere\Repositories\LocationRepo;

class LocationRepoServiceProvider extends ServiceProvider
{
	public function register()
	{
		$this->app->bind('Scubawhere\Repositories\LocationRepoInterface', function($app)
		{
			return new LocationRepo();
		});
	}	
}
