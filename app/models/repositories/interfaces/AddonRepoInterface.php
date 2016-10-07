<?php

namespace ScubaWhere\Repositories;

interface AddonRepoInterface {

	public function create($data);

	public function update($id, $update);

}