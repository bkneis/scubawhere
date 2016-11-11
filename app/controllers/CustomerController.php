<?php

use Scubawhere\Context;
use Scubawhere\Entities\Customer;
use Scubawhere\Entities\CrmSubscription;
use Scubawhere\Services\ObjectStoreService;
use Illuminate\Database\Eloquent\ModelNotFoundException;

class CustomerController extends Controller {

	protected $object_store_service;

	public function __construct(ObjectStoreService $object_store_service)
	{
		$this->object_store_service = $object_store_service;
	}

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
			return Response::json( array('errors' => array('Cannot create new customer! The email already exists.')), 409 ); // 409 Conflict

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

		$unsubscribe = Input::get('unsubscribe');
		$subscription_data = array();
		$subscription_data['token'] = 'USERACCEPTED';
		if($unsubscribe === 'true')
		{
			$subscription_data['subscribed'] = 0;
		}
		else
		{
			$subscription_data['subscribed'] = 1;
		}
		$subscription = new CrmSubscription($subscription_data);
		$customer->crmSubscription()->save($subscription);

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

		$unsubscribe = Input::get('unsubscribe');
		$subscription_data = array();
		$subscription_data['token'] = 'USERACCEPTED';
		if($unsubscribe === 'true')
		{
			$subscription_data['subscribed'] = 0;
		}
		else
		{
			$subscription_data['subscribed'] = 1;
		}
		$customer->crmSubscription()->update($subscription_data);

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

		$messages = array(
			'columns.required'      => 'Please specify which columns relate to your customers data',
			'customerData.required' => 'Please upload a valid CSV file with your customer data, ensuring it abides by the formatting guidelines'
		);

		$rules = array(
			'columns' => 'required',
			'customerData' => 'required'
		);

		$validator = Validator::make($data, $rules, $messages);

		if($validator->fails()) return Response::json(array('errors' => $validator->messages()->all()), 406);

		$columns = $data['columns'];
		$customer_data = $data['customerData'];

		$imported_customers = array();
		$imported_subscriptions = array();

		$errors = array();

		$csv_path = storage_path() . '/customer-imports-' . Context::get()->name . '.csv';

		$bytes_written = File::put($csv_path, "");
		if($bytes_written === false)
		{
			return Response::json(
				array('status' => 'Ooops, Something seems to have gone wrong. Please try upload '.
					'your customer data again. If the problem persists, please contact '.
					'support@scubawhere.com.')
				, 500);
		}

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
							// Remove whitespace
							$new_customer_data[$attr] = preg_replace('/\s+/', '', $customer[$index]);
						}
					}
				}
			}

			$new_customer = new Customer($new_customer_data);

			if( !$new_customer->validate() )
			{
				$error_msg = 'The customer data on row number ' . ($line_num  + 1) . ' was invalid';
				$err = array();
				$err["message"] = $error_msg;
				$err["errs"] = $new_customer->errors(); // @todo change this to ->all() and fix front end to display them correctly
				array_push($errors, $err);
				$err_csv_str = "";
				foreach($customer as $cust_attr)
				{
					$err_csv_str = $err_csv_str . $cust_attr . ",";
				}
				$err_csv_str = $err_csv_str . ",Errors - ,";
				foreach($err["errs"]->all() as $err_attr)
				{
					$err_csv_str = $err_csv_str . $err_attr . ",";
				}
				
				rtrim($err_csv_str, ",");
				$err_csv_str = $err_csv_str . "\n";
				$bytes_written = File::append($csv_path, $err_csv_str); // @todo somehow queue this method and then execute outside the loop to reduce disk IO
				if($bytes_written === false)
				{
					return Response::json(array('status' => 'Ooops, Something seems to have gone wrong. Please try upload your customer data again. If the problem persists, please contact support@scubawhere.com.'), 500);
				}
			}
			else
			{
				array_push($imported_customers, $new_customer);
			}

		}

		//return Response::json($imported_customers, 200);

		$this->object_store_service->uploadCustomerCSV($csv_path);

		$customers = Context::get()->customers()->saveMany($imported_customers);

		$subscription_data = array();
		$subscription_data['token'] = 'USERACCEPTED';
		$subscription_data['subscribed'] = 1;
		
		foreach($customers as $obj) 
		{
			$subscription = new CrmSubscription($subscription_data);
			if(!$subscription->validate())
			{
				return Response::json(
							array('errors' => 
								array('Oops, Something has gone wrong. We cannot create your customers email subscription.')
							), 500);
			}
			$obj->crmSubscription()->save($subscription);
		}

		return Response::json( array('status' => 'OK. Customers imported.', 'errors' => $errors, 'bytres' => $bytes_written), 200 ); // 200 OK

	}

	/**
	 * API Function to send the last failed import CSV file containing customers and their errors
	 * @return {File|JSON} Response 	A HTTP download promise to send the file over http to the client. Or on failure a json object with an error code.
	 */
	public function getLastImportErrors()
	{
		$url = $this->object_store_service->getCustomerCSVUrl();

		if($url) 
		{
			return Response::json(array('url' => $url), 200);
		}
		else
		{
			return Response::json(
				array('status' => 'BAD REQUEST. Apologies, something has gone wrong and we cant seem to find the file right now.'), 500);
		}

	}
}
