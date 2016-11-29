<?php 

namespace Scubawhere\Repositories;

use Illuminate\Support\ServiceProvider;
use Scubawhere\Repositories\PackageRepo;

class PackageRepoServiceProvider extends ServiceProvider
{
	public function register()
	{
		$this->app->bind('Scubawhere\Repositories\PackageRepoInterface', function($app)
		{
			return new PackageRepo();
		});
	}	
}
