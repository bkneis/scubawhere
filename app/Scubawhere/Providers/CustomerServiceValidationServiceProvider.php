<?php 

namespace Scubawhere\Providers;

use Illuminate\Support\ServiceProvider;
use Scubawhere\Decorators\CustomerValidation;

class CustomerValidationServiceProvider extends ServiceProvider
{
	public function register()
	{
		$this->app->bind('Scubawhere\Decorators\CustomerValidationInterface', function($app)
		{
			return new CustomerServiceValidator();
		});
	}	
}
