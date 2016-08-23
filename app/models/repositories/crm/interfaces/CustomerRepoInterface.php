<?php namespace ScubaWhere\Repositories;

interface CustomerRepoInterface {

	public function get($id);

	public function getAll();

	public function getFilter($filters);

	public function create($data);

	public function update($data);

	public function delete($id);

}

	
