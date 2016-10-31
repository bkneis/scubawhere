<?php 

namespace Scubawhere\Repositories;

use Illuminate\Support\ServiceProvider;
use Scubawhere\Repositories\PickupRepo;

class PickupRepoServiceProvider extends ServiceProvider
{
	public function register()
	{
		$this->app->bind('Scubawhere\Repositories\PickupRepoInterface', function($app)
		{
			return new PickupRepo();
		});
	}	
}
