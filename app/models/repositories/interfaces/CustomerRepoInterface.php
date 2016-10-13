<?php

namespace ScubaWhere\Repositories;

use ScubaWhere\Repositories\BaseRepoInterface;

interface CustomerRepoInterface {

	public function create($data);

	public function update($id, $data);

}