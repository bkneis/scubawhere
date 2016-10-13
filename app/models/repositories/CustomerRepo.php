<?php 

namespace ScubaWhere\Repositories;

use ScubaWhere\Context;
use ScubaWhere\Exceptions;
use ScubaWhere\Exceptions\InvalidInputException;
use ScubaWhere\Repositories\CustomerRepoInterface;
use Illuminate\Database\Eloquent\ModelNotFoundException;

class CustomerRepo /*extends BaseRepo*/ implements CustomerRepoInterface {

    /** 
     * Eloquent model that acts as the root model to associate assets to
     * @var \Company 
    */ 
    protected $company_model;

    public function __construct() {
        $this->company_model = Context::get();
    }

    /**
     * Get all customers for a company
     * @return \Illuminate\Database\Eloquent\Collection
     */
    public function all() {
        return \Customer::onlyOwners()->with('certificates', 'certificates.agency')->get();
    }

    /**
     * Get all customers for a company including soft deleted models
     * @return \Illuminate\Database\Eloquent\Collection 
     */
    public function allWithTrashed() {
        return \Customer::onlyOwners()->with('certificates', 'certificates.agency')->withTrashed()->get();
    }

    /**
     * Get an customer for a company from its id
     * @param  int   ID of the customer
     * @throws \Illuminate\Database\Eloquent\ModelNotFound
     * @return \Customer
     */
    public function get($id) {
        return \Customer::onlyOwners()->with('certificates', 'certificates.agency')->findOrFail($id);
    }

    /**
     * Get an customer for a company by a specified column and value
     * @param  string Column name to search by
     * @param  mixed  Value to match the customer
     * @return \Illuminate\Database\Eloquent\Collection
     */
    public function getWhere($query) {
        return \Customer::onlyOwners()->where($query)->with('certificates', 'certificates.agency')->get();
    }

    /**
     * Get all customers that have an email address
     * @return \Illuminate\Database\Eloquent\Collection
     */
    public function getAllWithEmails() 
    {
        return \Customer::onlyOwners()
            ->whereNotNull('email')
            ->where('email', '!=', '')
            ->get();    
    }

    public function getCustomersByCertification($certificate_ids) 
    {
        return \Customer::onlyOwners()
            ->whereHas('certificates', function ($query) use ($certificate_ids) {
                $query->whereIn('certificates.id', $certificate_ids);
            })
            ->whereNotNull('email')
            ->where('email', '!=', '')
            ->with('crmSubscription')
            ->get();
    }

    public function getCustomersByBookings($booking_ids) 
    {
        return \Customer::onlyOwners()
            ->whereIn('id', $booking_ids)
            ->whereNotNull('email')
            ->where('email', '!=', '')
            ->with('crmSubscription')
            ->get(); 
    }

    /**
     * Get all customers that met the filter parameters.
     * This is used as a search functionality for RMS
     * @param  string $firstname
     * @param  string $lastname
     * @param  string $email
     * @param  int    $from Optional parameter for number of customers to skip
     * @param  int    $take Optional parameter for number of customers to return
     * @return \Illuminate\Database\Eloquent\Collection
     */
    public function filter($firstname, $lastname, $email, $from, $take) 
    {
        if(empty($firstname) && empty($lastname) && empty($email)) {
            return $this->getAll();
        }

        $customers = \Customer::onlyOwners()
            ->where(function($query) use ($firstname)
            {
                if(!empty($firstname)) {
                    $query->where('firstname', '=', $firstname);
                }
            })
            ->where(function($query) use ($lastname)
            {
                if(!empty($lastname)) {
                    $query->where('lastname', '=', $lastname);
                }
            })
            ->where(function($query) use ($email)
            {
                if(!empty($email)) {
                    $query->where('email', '=', $email);
                }
            })
            ->orderBy('id', 'DESC')
            ->skip($from)
            ->take($take)
            ->get();

        return $customers; 
    }

    /**
     * Create an customer and associate it with its company
     * @param array Information about the customer to save
     * @throws \ScubaWhere\Exceptions\InvalidInputException
     * @return \Customer
     */
    public function create($data) {
        $customer = new \Customer($data);
        if (!$customer->validate()) {
            throw new InvalidInputException($customer->errors()->all());
        }
        return $this->company_model->customers()->save($customer);
    }

    public function associateCertificates($customer, $certificates)
    {
        try {
            $customer->certificates()->sync($certificates);
        }
        catch(\Exception $e) {
            throw new InvalidInputException(['There was an issue associating the certificates with the customer, please ensure all certificate ID\'s are valid']);
        }
        return $customer;
    }

    /**
     * Update an customer by id with specified data
     * @param  int   ID of the customer
     * @param  array Data to update the customer with
     * @throws \ScubaWhere\Exceptions\InvalidInputException
     * @return \Customer
     */
    public function update($id, $data) {
        $customer = $this->get($id);
        if(!$customer->update($data)) {
            throw new InvalidInputException($customer->errors()->all());
        }
        return $customer;
    }

    /**
     * Delete an customer by its id
     * @param  int ID of the customer
     * @throws Exception
     */
    public function delete($id) {
        $customer = $this->get($id);
        $customer->delete();
    }

    /**
     * Delete an customer by a specified column and value
     * @param  string Column name to search by
     * @param  mixed  Value to match the customer
     * @throws Exception
     */
    public function deleteWhere($query) {
        $customer = $this->getWhere($query);
        $customer->delete();
    }
}