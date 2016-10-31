<?php

namespace Scubawhere\Repositories;

interface TripRepoInterface {
	
	public function create($data, $locations, $tags);

	public function update($id, $data, $locations, $tags);

}