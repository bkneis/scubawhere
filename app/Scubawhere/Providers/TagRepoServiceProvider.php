<?php 

namespace Scubawhere\Repositories;

use Illuminate\Support\ServiceProvider;
use Scubawhere\Repositories\TagRepo;

class TagRepoServiceProvider extends ServiceProvider
{
	public function register()
	{
		$this->app->bind('Scubawhere\Repositories\TagRepoInterface', function($app)
		{
			return new TagRepo();
		});
	}	
}
