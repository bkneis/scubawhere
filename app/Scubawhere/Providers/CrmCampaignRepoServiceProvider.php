<?php 

namespace Scubawhere\Repositories;

use Illuminate\Support\ServiceProvider;
use Scubawhere\Repositories\CrmCampaignRepo;

class CrmCampaignRepoServiceProvider extends ServiceProvider
{
	public function register()
	{
		$this->app->bind('Scubawhere\Repositories\CrmCampaignRepoInterface', function($app)
		{
			return new CrmCampaignRepo();
		});
	}	
}
