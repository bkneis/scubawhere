<?php 

namespace ScubaWhere\Repositories;

use Illuminate\Support\ServiceProvider;
use ScubaWhere\Repositories\CrmGroupRepo;

class CrmGroupRepoServiceProvider extends ServiceProvider
{
	public function register()
	{
		$this->app->bind('ScubaWhere\Repositories\CrmGroupRepoInterface', function($app)
		{
			return new CrmGroupRepo();
		});
	}	
}
