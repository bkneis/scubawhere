<?php

namespace ScubaWhere\Repositories;

use ScubaWhere\Repositories\CacheRepoInterface;

class CacheRepo implements CacheRepoInterface {

	public function hasItem() { return; }

    public function all() { return; }

    public function allWithTrashed() { return; }

    public function get($id) { return; }

    public function getWhere($column, $value) { return; }

    public function create($data) { return; }

    public function update($id, $data) { return; }

    public function delete($id) { return; }

    public function deleteWhere($column, $value) { return; }
}
