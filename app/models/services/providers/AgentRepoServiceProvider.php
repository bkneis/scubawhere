<?php 

namespace ScubaWhere\Repositories;

use Illuminate\Support\ServiceProvider;
use ScubaWhere\Repositories\AgentRepo;

class AgentRepoServiceProvider extends ServiceProvider
{
	public function register()
	{
		$this->app->bind('ScubaWhere\Repositories\AgentRepoInterface', function($app)
		{
			return new AgentRepo();
		});
	}	
}
