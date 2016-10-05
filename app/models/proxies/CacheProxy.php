<?php

namespace ScubaWhere\Proxies;

use ScubaWhere\Repositories\BaseRepoInterface;
use ScubaWhere\Repositories\CacheRepoInterface;

class CacheProxy implements BaseRepoInterface {

	protected $repo;
	protected $cache_repo;
	
	public function __construct(BaseRepoInterface $repo, CacheRepoInterface $cache_repo) {
		$this->repo = $repo;
		$this->cache_repo = $cache_repo;
	}

    public function all() {
        return $this->repo->getAll();
    }

    public function allWithTrashed() {
    	return $this->repo->getAllWithTrashed();
    }

    public function get($id) {
        return $this->repo->get($id);
    }

    public function getWhere($column, $value) {
    	return $this->repo->getWhere($column, $value);
    }

    public function create($data) {
    	return $this->repo->create($date);
    }

    public function update($id, $data) {
        return $this->repo->update($id, $data);
    }

    public function delete($id) {
        return $this->repo->delete($id);
    }

    public function deleteWhere($column, $value) {
    	return $this->repo->deleteWhere($column, $value);
    }

}