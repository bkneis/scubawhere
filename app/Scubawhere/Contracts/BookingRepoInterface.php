<?php

namespace Scubawhere\Repositories;

use Scubawhere\Repositories\BaseRepoInterface;

interface BookingRepoInterface {

	public function create($data);

	public function update($id, $data);

}