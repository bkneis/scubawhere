<?php namespace ScubaWhere\Repositories;

class CustomerRepo implements CustomerRepoInterface {

	protected $company;

	public function __construct()
	{
		$this->company = Context::get();
	}

	public function get($id)
	{
		return $this->company->customers->findOrFail($id);
	}

	public function getAll()
	{
		return $this->company->customers()->get();
	}

	public function getFilter($filters)
	{
		$firstname = $filters['firstname'];
		$lastname  = $filters['lastname'];
		$email 	   = $filters['email'];

		if(empty($firstname) && empty($lastname) && empty($email))
			return $this->getAll();

		$customers = Context::get()->customers()
			->where(function($query) use ($firstname)
			{
				if(!empty($firstname))
					//$query->where('firstname', 'LIKE', '%'.$firstname.'%');
					$query->where('firstname', '=', $firstname);
			})
			->where(function($query) use ($lastname)
			{
				if(!empty($lastname))
					//$query->where('lastname', 'LIKE', '%'.$lastname.'%');
					$query->where('lastname', '=', $lastname);
			})
			->where(function($query) use ($email)
			{
				if(!empty($email))
					//$query->where('email', 'LIKE', '%'.$email.'%');
					$query->where('email', '=', $email);
			})
			->orderBy('id', 'DESC')
			->skip($from)
			->take($take)
			->get();

		return $customers;
	}

	public function create($data)
	{
		$customer = new Customer($data);
		if(!$customer->validate()) 
		{
			throw new InvalidInputException($customer->errors()->all();
		}
		return $customer;		
	}

	public function update($data)
	{
		$customer = $this->company->customers()->findOrFail($data['id']);
		if(!$customer->update($data)) 
		{
			throw InvalidInputException($customer->errors()->all());
		}
		return $customer;
	}

	public function delete($id)
	{
		$customer = $this->company->customers()->findOrFail($id);
		try
		{
			$customer->delete();
		}
		catch(Exception $e)
		{
			return false;
		}
		return true;
	}

}

	
