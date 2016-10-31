<?php 

namespace Scubawhere\Repositories;

use Illuminate\Support\ServiceProvider;
use Scubawhere\Repositories\CrmGroupRepo;

class CrmGroupRepoServiceProvider extends ServiceProvider
{
	public function register()
	{
		$this->app->bind('Scubawhere\Repositories\CrmGroupRepoInterface', function($app)
		{
			return new CrmGroupRepo();
		});
	}	
}
