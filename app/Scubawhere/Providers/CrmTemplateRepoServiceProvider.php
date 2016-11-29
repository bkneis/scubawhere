<?php 

namespace Scubawhere\Repositories;

use Illuminate\Support\ServiceProvider;
use Scubawhere\Repositories\CrmTemplateRepo;

class CrmTemplateRepoServiceProvider extends ServiceProvider
{
	public function register()
	{
		$this->app->bind('Scubawhere\Repositories\CrmTemplateRepoInterface', function($app)
		{
			return new CrmTemplateRepo();
		});
	}	
}
