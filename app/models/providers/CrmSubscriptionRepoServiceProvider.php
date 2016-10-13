<?php 

namespace ScubaWhere\Repositories;

use Illuminate\Support\ServiceProvider;
use ScubaWhere\Repositories\CrmSubscriptionRepo;

class CrmSubscriptionRepoServiceProvider extends ServiceProvider
{
	public function register()
	{
		$this->app->bind('ScubaWhere\Repositories\CrmSubscriptionRepoInterface', function($app)
		{
			return new CrmSubscriptionRepo();
		});
	}	
}
