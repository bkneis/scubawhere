<?php 

namespace Scubawhere\Repositories;

use Illuminate\Support\ServiceProvider;
use Scubawhere\Repositories\BoatroomRepo;

class BoatroomRepoServiceProvider extends ServiceProvider
{
	public function register()
	{
		$this->app->bind('Scubawhere\Repositories\BoatroomRepoInterface', function($app)
		{
			return new BoatroomRepo();
		});
	}	
}
