<?php

namespace ScubaWhere\Repositories;

interface LocationRepoInterface {

	public function create($data);

	public function update($id, $description);

}