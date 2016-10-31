<?php namespace Scubawhere\Repositories;

use Illuminate\Support\ServiceProvider;
use Scubawhere\Repositories\LogRepo;

class LogRepoServiceProvider extends ServiceProvider
{
	public function register()
	{
		$this->app->bind('Scubawhere\Repositories\LogRepoInterface', function($app)
		{
			return new LogRepo();
		});
	}	
}
