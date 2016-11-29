<?php 

namespace Scubawhere\Repositories;

use Illuminate\Support\ServiceProvider;
use Scubawhere\Repositories\TicketRepo;

class TicketRepoServiceProvider extends ServiceProvider
{
	public function register()
	{
		$this->app->bind('Scubawhere\Repositories\TicketRepoInterface', function($app)
		{
			return new TicketRepo();
		});
	}	
}
