<?php 

namespace Scubawhere\Repositories;

use Illuminate\Support\ServiceProvider;
use Scubawhere\Repositories\AgentRepo;

class AgentRepoServiceProvider extends ServiceProvider
{
	public function register()
	{
		$this->app->bind('Scubawhere\Repositories\AgentRepoInterface', function($app)
		{
			return new AgentRepo();
		});
	}	
}
