<?php 

namespace Scubawhere\Repositories;

use Illuminate\Support\ServiceProvider;
use Scubawhere\Repositories\AddonRepo;

class AddonRepoServiceProvider extends ServiceProvider
{
	public function register()
	{
		$this->app->bind('Scubawhere\Repositories\AddonRepoInterface', function($app)
		{
			return new AddonRepo();
		});
	}	
}
