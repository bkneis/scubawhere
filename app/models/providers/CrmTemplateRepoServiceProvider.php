<?php 

namespace ScubaWhere\Repositories;

use Illuminate\Support\ServiceProvider;
use ScubaWhere\Repositories\CrmTemplateRepo;

class CrmTemplateRepoServiceProvider extends ServiceProvider
{
	public function register()
	{
		$this->app->bind('ScubaWhere\Repositories\CrmTemplateRepoInterface', function($app)
		{
			return new CrmTemplateRepo();
		});
	}	
}
