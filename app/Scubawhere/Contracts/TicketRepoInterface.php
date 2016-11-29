<?php

namespace Scubawhere\Repositories;

interface TicketRepoInterface {

	public function getAvailable();

	public function create(array $data, array $trips, array $boats, array $boatrooms);

	public function update($id, array $data, array $trips, array $boats, array $boatrooms);

}