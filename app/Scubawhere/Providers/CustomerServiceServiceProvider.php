<?php 

namespace Scubawhere\Services;

use Scubawhere\Services\LogService;
use Scubawhere\Repositories\LogRepo;
use Illuminate\Support\ServiceProvider;
use Scubawhere\Services\CustomerService;
use Scubawhere\Repositories\CustomerRepo;
use Scubawhere\Repositories\ObjectStoreRepo;
use Scubawhere\Repositories\CrmSubscriptionRepo;
use Scubawhere\Decorators\CustomerServiceValidator;

/**
 * @todo How to inject dependencies using the IOC container for depedencies
 * injected by the service provider
 */
class CustomerServiceServiceProvider extends ServiceProvider
{
	public function register()
	{
		$this->app->bind('Scubawhere\Services\CustomerServiceInterface', function($app)
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
