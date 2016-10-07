<?php

namespace ScubaWhere\Repositories;

use ScubaWhere\Repositories\BaseRepoInterface;

interface PackageRepoInterface {

	public function all();

	public function allWithTrashed();

	public function get($id);

	public function getWhere($column, $value);

	public function create($data, $tickets, $courses, $accommodations, $addons);

	public function update($id, $data, $tickets, $courses, $accommodations, $addons);

	public function delete($id);

	public function deleteWhere($column, $value);

	public function begin();

	public function undo();

	public function finish();

}