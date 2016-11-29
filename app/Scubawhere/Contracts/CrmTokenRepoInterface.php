<?php

namespace Scubawhere\Repositories;

use Scubawhere\Repositories\BaseRepoInterface;

interface CrmTokenRepoInterface {

	public function create($campaign_id, $token, $customer_id);

}