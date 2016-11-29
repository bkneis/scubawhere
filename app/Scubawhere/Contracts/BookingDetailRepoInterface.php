<?php

namespace Scubawhere\Repositories;

use Scubawhere\Repositories\BaseRepoInterface;

interface BookingDetailRepoInterface {

	public function create($data, $temporary, $training);

}
