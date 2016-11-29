<?php 

namespace Scubawhere\Repositories;

use Illuminate\Support\ServiceProvider;
use Scubawhere\Repositories\CourseRepo;

class CourseRepoServiceProvider extends ServiceProvider
{
	public function register()
	{
		$this->app->bind('Scubawhere\Repositories\CourseRepoInterface', function($app)
		{
			return new CourseRepo();
		});
	}	
}
