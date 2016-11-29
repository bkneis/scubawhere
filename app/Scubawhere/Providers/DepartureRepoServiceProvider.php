<?php 

namespace Scubawhere\Repositories;

use Scubawhere\Entities\Departure;
use Illuminate\Support\ServiceProvider;
use Scubawhere\RepositoriesDepartureRepo;

class DepartureRepoServiceProvider extends ServiceProvider
{
	public function register()
	{
		$this->app->bind('Scubawhere\RepositoriesDepartureRepoInterface', function($app)
		{
			return new DepartureRepo();
		});
	}	
}
