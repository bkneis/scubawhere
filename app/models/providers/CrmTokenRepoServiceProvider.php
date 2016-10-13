<?php 

namespace ScubaWhere\Repositories;

use Illuminate\Support\ServiceProvider;
use ScubaWhere\Repositories\CrmTokenRepo;

class CrmTokenRepoServiceProvider extends ServiceProvider
{
	public function register()
	{
		$this->app->bind('ScubaWhere\Repositories\CrmTokenRepoInterface', function($app)
		{
			return new CrmTokenRepo();
		});
	}	
}
