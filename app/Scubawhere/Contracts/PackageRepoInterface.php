<?php

namespace Scubawhere\Repositories;

interface PackageRepoInterface {

    public function getAvailable();

    public function getUsedInFutureBookings($id, $fail = true);

}