<?php 

namespace ScubaWhere\Repositories;

use Illuminate\Support\ServiceProvider;
use ScubaWhere\Repositories\TrainingRepo;

class TrainingRepoServiceProvider extends ServiceProvider
{
	public function register()
	{
		$this->app->bind('ScubaWhere\Repositories\TrainingRepoInterface', function($app)
		{
			return new TrainingRepo();
		});
	}	
}
