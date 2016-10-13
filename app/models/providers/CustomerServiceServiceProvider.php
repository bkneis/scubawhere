<?php 

namespace ScubaWhere\Services;

use ScubaWhere\Services\LogService;
use ScubaWhere\Repositories\LogRepo;
use Illuminate\Support\ServiceProvider;
use ScubaWhere\Services\CustomerService;
use ScubaWhere\Repositories\CustomerRepo;
use ScubaWhere\Repositories\ObjectStoreRepo;
use ScubaWhere\Repositories\CrmSubscriptionRepo;
use ScubaWhere\Decorators\CustomerServiceValidator;

/**
 * @todo How to inject dependencies using the IOC container for depedencies
 * injected by the service provider
 */
class CustomerServiceServiceProvider extends ServiceProvider
{
	public function register()
	{
		$this->app->bind('ScubaWhere\Services\CustomerServiceInterface', function($app)
		{
			return new CustomerServiceValidator(
				new CustomerService(
					new CustomerRepo,
					new LogService(new LogRepo),
					new CrmSubscriptionRepo,
					new ObjectStoreService(new ObjectStoreRepo)
				)
			);
		});
	}	
}
