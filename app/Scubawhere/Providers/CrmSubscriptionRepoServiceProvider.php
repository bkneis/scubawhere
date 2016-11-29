<?php 

namespace Scubawhere\Repositories;

use Illuminate\Support\ServiceProvider;
use Scubawhere\Repositories\CrmSubscriptionRepo;

class CrmSubscriptionRepoServiceProvider extends ServiceProvider
{
	public function register()
	{
		$this->app->bind('Scubawhere\Repositories\CrmSubscriptionRepoInterface', function($app)
		{
			return new CrmSubscriptionRepo();
		});
	}	
}
