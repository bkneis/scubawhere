<?php 

namespace ScubaWhere\Repositories;

use Illuminate\Support\ServiceProvider;
use ScubaWhere\Repositories\CrmLinkTrackerRepo;

class CrmlinktrackerRepoServiceProvider extends ServiceProvider
{
	public function register()
	{
		$this->app->bind('ScubaWhere\Repositories\CrmLinkTrackerRepoInterface', function($app)
		{
			return new CrmLinkTrackerRepo();
		});
	}	
}
