<?php

namespace ScubaWhere\Repositories;

interface AgentRepoInterface {

	public function create($data);

	public function update($id, $update); 

}