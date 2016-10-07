<?php

namespace ScubaWhere\Repositories;

interface AccommodationRepoInterface {

	public function create($data);

	public function update($id, $data); 

}