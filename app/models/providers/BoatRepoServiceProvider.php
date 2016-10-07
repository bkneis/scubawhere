<?php 

namespace ScubaWhere\Repositories;

use Illuminate\Support\ServiceProvider;
use ScubaWhere\Repositories\BoatRepo;

class BoatRepoServiceProvider extends ServiceProvider
{
	public function register()
	{
		$this->app->bind('ScubaWhere\Repositories\BoatRepoInterface', function($app)
		{
			return new BoatRepo();
		});
	}	
}
