<?php

namespace ScubaWhere\Repositories;

interface PackageRepoInterface {

	public function create($data, $tickets, $courses, $accommodations, $addons);

	public function update($id, $data, $tickets, $courses, $accommodations, $addons);

}