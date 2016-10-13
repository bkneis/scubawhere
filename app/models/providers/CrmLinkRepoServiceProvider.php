<?php 

namespace ScubaWhere\Repositories;

use Illuminate\Support\ServiceProvider;
use ScubaWhere\Repositories\CrmLinkRepo;

class CrmLinkRepoServiceProvider extends ServiceProvider
{
	public function register()
	{
		$this->app->bind('ScubaWhere\Repositories\CrmLinkRepoInterface', function($app)
		{
			return new CrmLinkRepo();
		});
	}	
}
