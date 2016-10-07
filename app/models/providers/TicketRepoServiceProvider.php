<?php 

namespace ScubaWhere\Repositories;

use Illuminate\Support\ServiceProvider;
use ScubaWhere\Repositories\TicketRepo;

class TicketRepoServiceProvider extends ServiceProvider
{
	public function register()
	{
		$this->app->bind('ScubaWhere\Repositories\TicketRepoInterface', function($app)
		{
			return new TicketRepo();
		});
	}	
}
