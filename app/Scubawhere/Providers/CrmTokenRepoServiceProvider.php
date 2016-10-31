<?php 

namespace Scubawhere\Repositories;

use Illuminate\Support\ServiceProvider;
use Scubawhere\Repositories\CrmTokenRepo;

class CrmTokenRepoServiceProvider extends ServiceProvider
{
	public function register()
	{
		$this->app->bind('Scubawhere\Repositories\CrmTokenRepoInterface', function($app)
		{
			return new CrmTokenRepo();
		});
	}	
}
