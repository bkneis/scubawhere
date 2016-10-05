<?php 

namespace ScubaWhere\Repositories;

use Illuminate\Support\ServiceProvider;
use ScubaWhere\Repositories\PriceRepo;

class PriceRepoServiceProvider extends ServiceProvider
{
	public function register()
	{
		$this->app->bind('ScubaWhere\Repositories\PriceRepoInterface', function($app)
		{
			return new PriceRepo();
		});
	}	
}
