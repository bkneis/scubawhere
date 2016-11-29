<?php 

namespace Scubawhere\Repositories;

use Illuminate\Support\ServiceProvider;
use Scubawhere\Repositories\CustomerRepo;

class CustomerRepoServiceProvider extends ServiceProvider
{
	public function register()
	{
		$this->app->bind('Scubawhere\Repositories\CustomerRepoInterface', function($app)
		{
			return new CustomerRepo();
		});
	}	
}
