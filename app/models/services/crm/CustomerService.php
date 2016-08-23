<?php namespace ScubaWhere\Services;

class CustomerService {

	protected $customer_repo;

	public function __construct(CustomerRepoInterface $customer_repo)
	{
		$this->customer_repo = $customer_repo;
	}

	public function get($id)
	{
		if(!$id)
		{
			throw new InvalidInputException(array('The ID field is required.'));
		}

		return $this->customer_repo->get($id);
	}

	public function getAll()
	{
		return $this->customer_repo->getAll();
	}

	public function getFilter($filters)
	{
		return $this->customer_repo->getFilter($filters);	
	}

	public function create($data)
	{
		$customer = $this->customer_repo->create($data);
		$this->attachCertificates($customer, $data['certificats']);
		return $customer;
	}

	public function update($data)
	{
		$customer = $this->get($data['id']);
		if(! $customer->update($data))
		{
			throw new InvalidInputException($customer->errors()->all());
		}
		$this->attachCertificates($customer, $data['certificates']);
		return $customer;
	}

	//@todo create a customer import service
	public function importCsvData($data)
	{
		$columns = $data['columns'];
		$customer_data = $data['customerData'];

		$imported_customers = array();

		$errors = array();

		$csv_path = storage_path() . '/customer_imports/' . Context::get()->name . '.csv';

		$bytes_written = File::put($csv_path, "");
		if($bytes_written === false)
		{
			return Response::json(array('status' => 'Ooops, Something seems to have gone wrong. Please try upload your customer data again. If the problem persists, please contact support@scubawhere.com.'), 500);
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
							//var_dump($attr, $index, $customer);
							$new_customer_data[$attr] = $customer[$index];
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

		return true;

		//return $this->company->customers()->saveMany($imported_customers);
	}

	public function getLastImportErrors()
	{
		$csv_path = storage_path() . "/customer_imports/" . $this->company->name . ".csv";

		if(File::exists($csv_path))
		{
			$file = File::get($csv_path);
			$headers = array('Content-Type' => 'text/csv');
			return Response::download($csv_path, 'invalid_import_data.csv', $headers);
		}
		else
		{
			return Response::json(array('status' => 'BAD REQUEST. Apologies, something has gone wrong and we cant seem to find the file right now.'), 500);
		}
		
	}

	protected function attachCertificates($customer, $certificates)
	{
		if(is_array($certificates) && (count($certificates) === 0 || is_numeric($certificates[0])))
		{
			try
			{
				$customer->certificates()->sync($certificates);
			}
			catch(Exception $e)
			{
				// @todo change this to BadRequestException
				return Response::json( array('errors' => array('Could not assign certificates to customer, \'certificates\' array is propably erroneous.')), 400 ); // 400 Bad Request
			}
		}
	}

}

	
