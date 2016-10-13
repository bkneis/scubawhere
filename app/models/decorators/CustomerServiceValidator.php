<?php

namespace ScubaWhere\Decorators;

use ScubaWhere\Services\CustomerServiceInterface;

class CustomerServiceValidator implements CustomerServiceInterface {

	protected $customer_service;

	public function __construct(CustomerServiceInterface $customer_service)
	{
		$this->customer_service = $customer_service;
	}

	public function get($id) 
	{
		return $this->customer_service->get($id);
	}

	public function getAll()
	{
		return $this->customer_service->getAll();
	}

	public function getAllWithTrashed() 
	{
		return $this->customer_service->getAllWithTrashed();
	}

	public function filter($firstname, $lastname, $email, $from = 0, $take = 20) 
	{
		return $this->customer_service->filter($firstname, $lastname, $email, $from, $take);
	}

	public function create($data, $certificates, $unsubscribe) 
	{
		return $this->customer_service->create($data, $certificates, $unsubscribe);
	}

	public function update($id, $data, $certiciates, $unsubscribe) 
	{
    	return $this->customer_service->update($id, $data, $certiciates, $unsubscribe);
	}

	public function delete($id)
	{
		return $this->customer_service->delete();	
	}

	public function importCustomers($data)
	{
		$messages = array(
			'columns.required'      => 'Please specify which columns relate to your customers data',
			'customerData.required' => 'Please upload a valid CSV file with your customer data, ensuring it abides by the formatting guidelines'
		);

		$rules = array(
			'columns'      => 'required',
			'customerData' => 'required'
		);

		$validator = \Validator::make($data, $rules, $messages);

		if($validator->fails()) {
			throw new InvalidInputException($validator->messages()->all());
		}

		$this->customer_service->importCustomers($data);
	}

}

