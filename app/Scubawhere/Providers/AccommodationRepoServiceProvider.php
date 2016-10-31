<?php 

namespace Scubawhere\Repositories;

use Illuminate\Support\ServiceProvider;
use Scubawhere\Repositories\AccommodationRepo;

class AccommodationRepoServiceProvider extends ServiceProvider
{
	public function register()
	{
		$this->app->bind('Scubawhere\Repositories\AccommodationRepoInterface', function($app)
		{
			return new AccommodationRepo();
		});
	}	
}
