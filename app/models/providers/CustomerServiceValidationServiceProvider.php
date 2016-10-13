<?php 

namespace ScubaWhere\Providers;

use Illuminate\Support\ServiceProvider;
use ScubaWhere\Decorators\CustomerValidation;

class CustomerValidationServiceProvider extends ServiceProvider
{
	public function register()
	{
use 
		$this->app->bind('ScubaWhere\Decorators\CustomerValidationInterface', function($app)
		{
			return new CustomerServiceValidator();
		});
	}	
}
