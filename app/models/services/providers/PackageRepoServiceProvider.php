<?php 

namespace ScubaWhere\Repositories;

use Illuminate\Support\ServiceProvider;
use ScubaWhere\Repositories\PackageRepo;

class PackageRepoServiceProvider extends ServiceProvider
{
	public function register()
	{
		$this->app->bind('ScubaWhere\Repositories\PackageRepoInterface', function($app)
		{
			return new PackageRepo();
		});
	}	
}
