<?php 

namespace ScubaWhere\Repositories;

use Illuminate\Support\ServiceProvider;
use ScubaWhere\Repositories\CrmGroupRuleRepo;

class CrmGroupRuleRepoServiceProvider extends ServiceProvider
{
	public function register()
	{
		$this->app->bind('ScubaWhere\Repositories\CrmGroupRuleRepoInterface', function($app)
		{
			return new CrmGroupRuleRepo();
		});
	}	
}
