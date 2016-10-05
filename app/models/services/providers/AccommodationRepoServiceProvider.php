<?php 

namespace ScubaWhere\Repositories;

use Illuminate\Support\ServiceProvider;
use ScubaWhere\Repositories\AccommodationRepo;

class AccommodationRepoServiceProvider extends ServiceProvider
{
	public function register()
	{
		$this->app->bind('ScubaWhere\Repositories\AccommodationRepoInterface', function($app)
		{
			return new AccommodationRepo();
		});
	}	
}
