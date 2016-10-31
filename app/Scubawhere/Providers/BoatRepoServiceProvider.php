<?php 

namespace Scubawhere\Repositories;

use Illuminate\Support\ServiceProvider;
use Scubawhere\Repositories\BoatRepo;

class BoatRepoServiceProvider extends ServiceProvider
{
	public function register()
	{
		$this->app->bind('Scubawhere\Repositories\BoatRepoInterface', function($app)
		{
			return new BoatRepo();
		});
	}	
}
