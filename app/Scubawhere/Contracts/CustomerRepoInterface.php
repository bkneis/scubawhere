<?php

namespace Scubawhere\Repositories;

use Scubawhere\Repositories\BaseRepoInterface;

interface CustomerRepoInterface {

	public function create($data);

	public function getAllWithEmails();

	public function getCustomersByCertification(array $certificate_ids);

	public function getCustomersByBookings(array $booking_ids);

	public function filter($firstname, $lastname, $email, $from, $take);

}