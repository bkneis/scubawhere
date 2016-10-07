<?php 

namespace ScubaWhere\Repositories;

use Illuminate\Support\ServiceProvider;
use ScubaWhere\Repositories\BoatroomRepo;

class BoatroomRepoServiceProvider extends ServiceProvider
{
	public function register()
	{
		$this->app->bind('ScubaWhere\Repositories\BoatroomRepoInterface', function($app)
		{
			return new BoatroomRepo();
		});
	}	
}
