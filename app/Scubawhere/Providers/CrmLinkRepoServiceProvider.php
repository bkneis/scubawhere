<?php 

namespace Scubawhere\Repositories;

use Illuminate\Support\ServiceProvider;
use Scubawhere\Repositories\CrmLinkRepo;

class CrmLinkRepoServiceProvider extends ServiceProvider
{
	public function register()
	{
		$this->app->bind('Scubawhere\Repositories\CrmLinkRepoInterface', function($app)
		{
			return new CrmLinkRepo();
		});
	}	
}
