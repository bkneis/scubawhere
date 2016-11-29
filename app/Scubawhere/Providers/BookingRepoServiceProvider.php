<?php 

namespace Scubawhere\Repositories;

use Illuminate\Support\ServiceProvider;
use Scubawhere\Repositories\BookingRepo;

class BookingRepoServiceProvider extends ServiceProvider
{
	public function register()
	{
		$this->app->bind('Scubawhere\Repositories\BookingRepoInterface', function($app)
		{
			return new BookingRepo();
		});
	}	
}
