<?php 

namespace ScubaWhere\Repositories;

use Illuminate\Support\ServiceProvider;
use ScubaWhere\Repositories\AddonRepo;

class AddonRepoServiceProvider extends ServiceProvider
{
	public function register()
	{
		$this->app->bind('ScubaWhere\Repositories\AddonRepoInterface', function($app)
		{
			return new AddonRepo();
		});
	}	
}
