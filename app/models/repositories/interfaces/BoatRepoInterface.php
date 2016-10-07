<?php

namespace ScubaWhere\Repositories;

interface BoatRepoInterface {

	public function create($data);

	public function update($id, $data);

}