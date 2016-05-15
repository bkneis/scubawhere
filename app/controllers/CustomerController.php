<?php
use Illuminate\Database\Eloquent\ModelNotFoundException;
use ScubaWhere\Context;

class CustomerController extends Controller {

	public function getIndex()
	{
		try
		{
			if( !Input::get('id') ) throw new ModelNotFoundException();
			return Context::get()->customers()->with('certificates', 'certificates.agency')->findOrFail( Input::get('id') );
		}
		catch(ModelNotFoundException $e)
		{
			return Response::json( array('errors' => array('The customer could not be found.')), 404 ); // 404 Not Found
		}
	}

	public function getAll()
	{
		return Context::get()->customers()->with('certificates', 'certificates.agency')->get();
	}

	public function getFilter($from = 0, $take = 20)
	{
		/**
		 * Allowed input parameter
		 * firstname  {string}
		 * lastname   {string}
		 * email      {string}
		 */

		$firstname = Input::get('firstname', null);
		$lastname = Input::get('lastname', null);
		$email = Input::get('email', null);

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

	public function postAdd()
	{
		$data = Input::only(
			'email',
			'firstname',
			'lastname',
			'birthday',
			'gender',
			'address_1',
			'address_2',
			'city',
			'county',
			'postcode',
			'country_id',
			'phone',
			'last_dive',
			'number_of_dives',
			'chest_size',
			'shoe_size',
			'height'
		);

		$customer = new Customer($data);

		if( !$customer->validate() )
		{
			return Response::json( array('errors' => $customer->errors()->all()), 406 ); // 406 Not Acceptable
		}

		// Validate that the customer does not already exist within the logged-in company
		if( !empty($data['email']) && Context::get()->customers()->where('email', $data['email'])->count() > 0 )
			return Response::json( array('errors' => 'Cannot create new customer! The email already exists.'), 409 ); // 409 Conflict

		$customer = Context::get()->customers()->save($customer);

		$certificates = Input::get('certificates', []);
		if( $certificates && !empty($certificates) && is_array($certificates) && (count($certificates) === 0 || is_numeric($certificates[0])) )
		{
			try
			{
				$customer->certificates()->sync($certificates);
			}
			catch(Exception $e)
			{
				return Response::json( array('errors' => array('Could not assign certificates to customer, \'certificates\' array is propably erroneous.')), 400 ); // 400 Bad Request
			}
		}

		return Response::json( array('status' => 'OK. Customer created', 'id' => $customer->id), 201 ); // 201 Created
	}

	public function postEdit()
	{
		try
		{
			if( !Input::get('id') ) throw new ModelNotFoundException();
			$customer = Context::get()->customers()->findOrFail( Input::get('id') );
		}
		catch(ModelNotFoundException $e)
		{
			return Response::json( array('errors' => array('The customer could not be found.')), 404 ); // 404 Not Found
		}

		$data = Input::only(
			'email',
			'firstname',
			'lastname',
			'birthday',
			'gender',
			'address_1',
			'address_2',
			'city',
			'county',
			'postcode',
			'country_id',
			'phone',
			'last_dive',
			'number_of_dives',
			'chest_size',
			'shoe_size',
			'height'
		);

		if( !$customer->update($data) )
		{
			return Response::json( array('errors' => $customer->errors()->all()), 406 ); // 406 Not Acceptable
		}

		$certificates = Input::get('certificates', []);
		if( is_array($certificates) && (count($certificates) === 0 || is_numeric($certificates[0])) )
		{
			try
			{
				$customer->certificates()->sync($certificates);
			}
			catch(Exception $e)
			{
				return Response::json( array('errors' => array('Could not assign certificates to customer, \'certificates\' array is propably erroneous.')), 400 ); // 400 Bad Request
			}
		}

		return Response::json( array('status' => 'OK. Customer updated.'), 200 ); // 200 OK
	}

	/**
	 * API Function to import customer data saved as a CSV then create and validate them as customers within the system.
	 * @param {Array} columns 				An array where the index represents the column number and the value which attribute it should map to the customer. "" is null
	 * @param {Array} customerData  		An array which holds an array for each customer, where each attribute is its own field in the array
	 * @return {ResponseObject} Response 	A HTTP response object, 200 for OK all customers created, 406 for invalid customers
	 */
	public function postImportcsv()
	{
		$data = Input::all();
		$columns = $data["columns"];
		$customer_data = $data["customerData"];

		$imported_customers = array();

		$errors = array();

		foreach($customer_data as $line_num => $customer)
		{
			$new_customer_data = array();
			foreach($columns as $index => $attr)
			{
				if(!empty($attr))
				{
					if(array_key_exists($index, $customer))
					{
						if(!empty($customer[$index]))
						{
							//var_dump($attr, $index, $customer);
							$new_customer_data[$attr] = $customer[$index];
						}
					}
				}
			}

			$new_customer = new Customer($new_customer_data);

			if( !$new_customer->validate() )
			{
				$error_msg = "The customer data on row number " . ($line_num  + 1) . " was invalid";
				$err = array();
				$err["message"] = $error_msg;
				$err["errs"] = $new_customer->errors();
				array_push($errors, $err);
			}

			array_push($imported_customers, $new_customer);
		}

		//Context::get()->customers()->saveMany($imported_customers);

		return Response::json( array('status' => 'OK. Customers imported.', 'customers' => $imported_customers, 'errors' => $errors), 200 ); // 200 OK

	}
}
