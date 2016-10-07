<?php 

namespace ScubaWhere\Repositories;

use Illuminate\Support\Facades\DB;
use ScubaWhere\Repositories\TripRepo;
use Illuminate\Support\ServiceProvider;

class TripRepoServiceProvider extends ServiceProvider
{
	public function register()
	{
		$this->app->bind('ScubaWhere\Repositories\TripRepoInterface', function($app)
		{
			return new TripRepo(new DB);
		});
	}	
}
