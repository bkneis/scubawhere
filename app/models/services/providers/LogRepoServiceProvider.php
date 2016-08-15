<?php namespace ScubaWhere\Repositories;

use Illuminate\Support\ServiceProvider;
use ScubaWhere\Repositories\LogRepo;

class LogRepoServiceProvider extends ServiceProvider
{
	public function register()
	{
		$this->app->bind('ScubaWhere\Repositories\LogRepoInterface', function($app)
		{
			return new LogRepo();
		});
	}	
}
