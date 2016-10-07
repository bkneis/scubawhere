<?php

namespace ScubaWhere\Repositories;

interface TrainingRepoInterface {

	public function create($data);

	public function update($id, $data);

}