<?php

namespace ScubaWhere\Repositories;

interface CourseRepoInterface {

	public function create($data);

	public function update($id, $data);

}