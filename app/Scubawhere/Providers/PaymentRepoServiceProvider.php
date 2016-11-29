<?php 

namespace Scubawhere\Repositories;

use Illuminate\Support\ServiceProvider;
use Scubawhere\Repositories\PaymentRepo;

class PaymentRepoServiceProvider extends ServiceProvider
{
	public function register()
	{
		$this->app->bind('Scubawhere\Repositories\PaymentRepoInterface', function($app)
		{
			return new PaymentRepo();
		});
	}	
}
