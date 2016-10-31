<?php 

namespace Scubawhere\Repositories;

use Illuminate\Support\ServiceProvider;
use Scubawhere\Repositories\CrmLinkTrackerRepo;

class CrmlinktrackerRepoServiceProvider extends ServiceProvider
{
	public function register()
	{
		$this->app->bind('Scubawhere\Repositories\CrmLinkTrackerRepoInterface', function($app)
		{
			return new CrmLinkTrackerRepo();
		});
	}	
}
