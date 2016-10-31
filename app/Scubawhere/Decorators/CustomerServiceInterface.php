<?php

namespace Scubawhere\Services;

interface CustomerServiceInterface {

	public function get($id);

	public function getAll();

	public function getAllWithTrashed();
	
	public function filter($firstname, $lastname, $email, $from = 0, $take = 20);

	public function create($data, $certificates, $unsubscribe);

	public function update($id, $data, $certificates, $unsubscribe);

	public function delete($id);

	public function importCustomers($data);

}