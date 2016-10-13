<?php

namespace ScubaWhere\Repositories;

use ScubaWhere\Repositories\BaseRepoInterface;

interface CrmGroupRepoInterface {

	public function create($data);

	public function update($id, $data);

}