<?php

namespace ScubaWhere\Repositories;

use ScubaWhere\Repositories\BaseRepoInterface;

interface TicketRepoInterface {

	public function all();

	public function allWithTrashed();

	public function get($id);

	public function getWhere($column, $value);

	public function getAvailable();

	public function create($data, $trips, $boats, $boatrooms);

	public function update($id, $data, $trips, $boats, $boatrooms);

	public function delete($id);

	public function deleteWhere($column, $value);

}