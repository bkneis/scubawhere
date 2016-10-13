<?php

namespace ScubaWhere\Repositories;

use ScubaWhere\Repositories\BaseRepoInterface;

interface CrmTokenRepoInterface {

	public function create($campaign_id, $token, $customer_id);

	public function update($id, $data);

}