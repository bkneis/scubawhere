<?php 

namespace ScubaWhere\Repositories;

use Illuminate\Support\ServiceProvider;
use ScubaWhere\Repositories\CourseRepo;

class CourseRepoServiceProvider extends ServiceProvider
{
	public function register()
	{
		$this->app->bind('ScubaWhere\Repositories\CourseRepoInterface', function($app)
		{
			return new CourseRepo();
		});
	}	
}
