<?php 

namespace ScubaWhere\Repositories;

use Illuminate\Support\ServiceProvider;
use ScubaWhere\Repositories\CrmCampaignRepo;

class CrmCampaignRepoServiceProvider extends ServiceProvider
{
	public function register()
	{
		$this->app->bind('ScubaWhere\Repositories\CrmCampaignRepoInterface', function($app)
		{
			return new CrmCampaignRepo();
		});
	}	
}
