<?php

namespace ScubaWhere\Repositories;

interface TripRepoInterface {

	public function all();

	public function allWithTrashed();

	public function get($id);

	public function getWhere($column, $value);

	public function create($data, $locations, $tags);

	public function update($id, $data, $locations, $tags);

	public function delete($id);

	public function deleteWhere($column, $value);

}