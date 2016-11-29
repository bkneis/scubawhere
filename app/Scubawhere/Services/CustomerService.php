<?php

namespace Scubawhere\Services;

use Scubawhere\Context;
use Scubawhere\Exceptions\Http\HttpInternalServerError;
use Scubawhere\Repositories\CustomerRepoInterface;
use Scubawhere\Exceptions\MethodNotSupportedException;
use Scubawhere\Repositories\CrmSubscriptionRepoInterface;

class CustomerService implements CustomerServiceInterface {

	/** @var \Scubawhere\Repositories\CustomerRepo */
	protected $customer_repo;

	/**
	 * Service used to log issues to trouble shooting when waterfall deleting
	 * @var \Scubawhere\Services\LogService
	 */
	protected $log_service;

	/** @var \Scubawhere\Repositories\CrmSubscriptionRepo */
	protected $crm_subscription_repo;

	/**
	 * Service used to access AWS S3 bucket
	 * @var \Scubawhere\Services\ObjectStoreService
	 */
	protected $object_store_service;


	public function __construct(CustomerRepoInterface $customer_repo,
								LogService $log_service,
								CrmSubscriptionRepoInterface $crm_subscription_repo,
								ObjectStoreService $object_store_service) 
	{
		$this->customer_repo         = $customer_repo;
		$this->log_service           = $log_service;
		$this->crm_subscription_repo = $crm_subscription_repo;
		$this->object_store_service  = $object_store_service;
	}

	/**
     * Get an customer for a company from its id
	 *
     * @param int $id ID of the customer
     *
     * @return \Scubawhere\Entities\Customer
     */
	public function get($id) {
		return $this->customer_repo->get($id, ['certificates', 'certificates.agency']);
	}

	/**
     * Get all customers for a company
	 *
     * @return \Illuminate\Database\Eloquent\Collection
     */
	public function getAll() {
		return $this->customer_repo->all(['certificates', 'certificates.agency']);
	}

	/**
     * Get all customers for a company including soft deleted models
	 *
     * @return \Illuminate\Database\Eloquent\Collection
     */
	public function getAllWithTrashed() {
		return $this->customer_repo->allWithTrashed(['certificates', 'certificates.agency']);
	}

	public function filter($firstname, $lastname, $email, $from = 0, $take = 20) 
	{
		return $this->customer_repo->filter($firstname, $lastname, $email, $from, $take);
	}

	/**
	 * Validate, create and save the customer and prices to the database
	 *
	 * @param array $data Data to autofill customer model
	 *
	 * @return \Scubawhere\Entities\Customer
	 */
	public function create($data, $certificates, $unsubscribe) 
	{
		$customer = $this->customer_repo->create($data);
		$this->customer_repo->associateCertificates($customer, $certificates);
		$subscription_data = array();
		$subscription_data['token'] = 'USERACCEPTED';
		if($unsubscribe === 'true') {
			$subscription_data['subscribed'] = 0;
		}
		else {
			$subscription_data['subscribed'] = 1;
		}
		$subscription = $this->crm_subscription_repo->create($subscription_data);
		$customer->crmSubscription()->save($subscription);
		return $customer;
	}

	/**
	 * Validate, update and save the customer and prices to the database
	 *
	 * @param  int   $id           ID of the customer
	 * @param  array $data         Information about customer
	 *
	 * @return \Scubawhere\Entities\Customer
	 */
	public function update($id, $data, $certificates, $unsubscribe) 
	{
    	$customer = $this->customer_repo->update($id, $data);
		$this->customer_repo->associateCertificates($customer, $certificates);
		$subscription_data = array();
		$subscription_data['token'] = 'USERACCEPTED';
		if($unsubscribe === 'true') {
			$subscription_data['subscribed'] = 0;
		}
		else {
			$subscription_data['subscribed'] = 1;
		}
		$customer->crmSubscription()->update($subscription_data);
		return $customer;
	}

	/**
	 * @throws MethodNotSupportedException
	 * @todo   Implement this function
	 */
	public function delete($id)
	{
		throw new MethodNotSupportedException(['error']);
	}

	/**
	 * Import customer data from external sources and insert relevant data into the database.
	 * 
	 * @param  array $data Content seperated by lines, indexed by line number
	 *
	 * @throws HttpInternalServerError
	 * 
	 * @return array Customers and their info extracted from the CSV file
	 */
	public function importCustomers($data) 
	{
		$columns = $data['columns'];
		$customer_data = $data['customerData'];

		$imported_customers = array();
		$imported_subscriptions = array();
		$errors = array();

		$csv_path = storage_path() . '/customer-imports-' . Context::get()->name . '.csv';

		$bytes_written = \File::put($csv_path, "");
		if($bytes_written === false) {
			throw new HttpInternalServerError(__CLASS__ . __METHOD__);
		}

		$result = $this->extractCustomersFromCSV($customer_data, $columns, $csv_path);

		$this->object_store_service->uploadCustomerCSV($csv_path);

		$customers = Context::get()->customers()->saveMany($result['customers']);

		$subscription_data = array();
		$subscription_data['token'] = 'USERACCEPTED';
		$subscription_data['subscribed'] = 1;
		
		foreach($customers as $obj) 
		{
			$subscription = new \CrmSubscription($subscription_data);
			if(!$subscription->validate())
			{
				throw new HttpInternalServerError(__CLASS__.__METHOD__);
			}
			$obj->crmSubscription()->save($subscription);
		}
		return $result;
	}

	/**
	 * Import customer data from external sources and insert relevant data into the database.
	 *
	 * @param array  $customer_data Content seperated by lines, indexed by line number
	 * @param array  $columns       The attribute that the column in the csv represents
	 * @param string $csv_path      Path to the temporary csv file
	 *
	 * @throws HttpInternalServerError
	 *
	 * @return array $imported_customers Array of Customer models created from the first parameter
	 */
	private function extractCustomersFromCSV(array $customer_data, array $columns, $csv_path)
	{
		$result             = array();
		$imported_customers = array();
		$errors             = array();

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

			$new_customer = new \Customer($new_customer_data);

			if( !$new_customer->validate() )
			{
				$error_msg = 'The customer data on row number ' . ($line_num  + 1) . ' was invalid';
				$err = array();
				$err["message"] = $error_msg;
				$err["errs"] = $new_customer->errors(); // @todo change this to ->all() and fix front end to display them correctly
				array_push($errors, $err);
				$err_csv_str = "";
				foreach($customer as $cust_attr) {
					$err_csv_str = $err_csv_str . $cust_attr . ",";
				}

				$err_csv_str = $err_csv_str . ",Errors - ,";

				foreach($err["errs"]->all() as $err_attr) {
					$err_csv_str = $err_csv_str . $err_attr . ",";
				}
				
				rtrim($err_csv_str, ",");
				$err_csv_str = $err_csv_str . "\n";
				$bytes_written = \File::append($csv_path, $err_csv_str); // @todo somehow queue this method and then execute outside the loop to reduce disk IO
				if($bytes_written === false) {
					throw new HttpInternalServerError(__CLASS__.__METHOD__);
				}
			}
			else {
				array_push($imported_customers, $new_customer);
			}
		}
		$result['customers'] = $imported_customers;
		$result['errors']    = $errors;

		return $result;	
	}

}
