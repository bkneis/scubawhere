<?php

namespace ScubaWhere\Repositories;

interface TicketRepoInterface {

	public function getAvailable();

	public function create($data, $trips, $boats, $boatrooms);

	public function update($id, $data, $trips, $boats, $boatrooms);

}