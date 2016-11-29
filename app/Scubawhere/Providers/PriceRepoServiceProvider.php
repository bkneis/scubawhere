<?php 

namespace Scubawhere\Repositories;

use Illuminate\Support\ServiceProvider;
use Scubawhere\Repositories\PriceRepo;

class PriceRepoServiceProvider extends ServiceProvider
{
	public function register()
	{
		$this->app->bind('Scubawhere\Repositories\PriceRepoInterface', function($app)
		{
			return new PriceRepo();
		});
	}	
}
