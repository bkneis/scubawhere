<?php 

namespace Scubawhere\Repositories;

use Illuminate\Support\Facades\DB;
use Scubawhere\Repositories\TripRepo;
use Illuminate\Support\ServiceProvider;

class TripRepoServiceProvider extends ServiceProvider
{
	public function register()
	{
		$this->app->bind('Scubawhere\Repositories\TripRepoInterface', function($app)
		{
			return new TripRepo(new DB);
		});
	}	
}
