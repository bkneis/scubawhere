<?php

namespace ScubaWhere\Repositories;

use ScubaWhere\Repositories\BaseRepoInterface;

interface CrmGroupRuleRepoInterface {

	public function create($data);

	public function update($id, $data);

}