<?php 

namespace Scubawhere\Repositories;

use Illuminate\Support\ServiceProvider;
use Scubawhere\Repositories\CrmGroupRuleRepo;

class CrmGroupRuleRepoServiceProvider extends ServiceProvider
{
	public function register()
	{
		$this->app->bind('Scubawhere\Repositories\CrmGroupRuleRepoInterface', function($app)
		{
			return new CrmGroupRuleRepo();
		});
	}	
}
