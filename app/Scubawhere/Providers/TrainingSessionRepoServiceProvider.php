<?php 

namespace Scubawhere\Repositories;

use Illuminate\Support\ServiceProvider;
use Scubawhere\Repositories\TrainingSessionRepo;

class TrainingSessionRepoServiceProvider extends ServiceProvider
{
	public function register()
	{
		$this->app->bind('Scubawhere\Repositories\TrainingSessionRepoInterface', function($app)
		{
			return new TrainingSessionRepo();
		});
	}	
}
