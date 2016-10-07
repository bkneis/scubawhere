<?php

namespace ScubaWhere\Repositories;

interface PriceRepoInterface {

	public function create($data);

	public function update($id, $data);	

}