<?php

namespace Scubawhere\Repositories;

interface PackageRepoInterface {

	public function create(array $data, array $tickets, array $courses, array $accommodations, array $addons);

	public function update($id, array $data, array $tickets, array $courses, array $accommodations, array $addons, $fail = true);

}