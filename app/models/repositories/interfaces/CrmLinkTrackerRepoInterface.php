<?php

namespace ScubaWhere\Repositories;

use ScubaWhere\Repositories\BaseRepoInterface;

interface CrmLinkTrackerRepoInterface /*extends BaseRepo*/ {

	public function create($data);

	public function update($id, $data);

}