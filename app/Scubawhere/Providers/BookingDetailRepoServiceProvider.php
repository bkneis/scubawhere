<?php 

namespace Scubawhere\Repositories;

use Illuminate\Support\ServiceProvider;
use Scubawhere\Repositories\BookingDetailRepo;

class BookingDetailRepoServiceProvider extends ServiceProvider
{
	public function register()
	{
		$this->app->bind('Scubawhere\Repositories\BookingDetailRepoInterface', function($app)
		{
			return new BookingDetailRepo();
		});
	}	
}
