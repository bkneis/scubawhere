<?php 

namespace Scubawhere\Repositories;

use Illuminate\Support\ServiceProvider;
use Scubawhere\Repositories\TrainingRepo;

class TrainingRepoServiceProvider extends ServiceProvider
{
	public function register()
	{
		$this->app->bind('Scubawhere\Repositories\TrainingRepoInterface', function($app)
		{
			return new TrainingRepo();
		});
	}	
}
