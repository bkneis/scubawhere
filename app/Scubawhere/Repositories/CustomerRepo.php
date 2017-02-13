<?php 

namespace Scubawhere\Repositories;

use Illuminate\Database\QueryException;
use Scubawhere\Context;
use Scubawhere\Exceptions;
use Scubawhere\Entities\Customer;
use Scubawhere\Exceptions\Http\HttpNotFound;
use Scubawhere\Exceptions\Http\HttpNotAcceptable;
use Scubawhere\Exceptions\Http\HttpUnprocessableEntity;
use Scubawhere\Exceptions\InvalidInputException;

/**
 * Class CustomerRepo acts as a DAO for the accommodation models.
 *
 * It should always return eloquent models and can be thought of as a collection. If at any point in the project, you
 * find yourself accessing the model to retrieve the same data, please add a function to this class and use that.
 *
 * @package Scubawhere\Repositories
 *
 * @see \Scubawhere\Repositories\BaseRepo
 * @see \Scubawhere\Repositories\CustomerRepoInterface
 */
class CustomerRepo extends BaseRepo implements CustomerRepoInterface {

    /** @var \ScubaWhere\Entities\Company */
    protected $company_model;

    public function __construct() {
        $this->company_model = Context::get();
    }

    /**
     * Get all customers for a company
     *
     * @param array $relations
     *
     * @return \Illuminate\Database\Eloquent\Collection
     */
    public function all(array $relations = []) {
        return Customer::onlyOwners()->with($relations)->get();
        //return Customer::onlyOwners()->with('certificates', 'certificates.agency')->get();
    }

    /**
     * Get all customers for a company including soft deleted models
     *
     * @param array $relations
     *
     * @return \Illuminate\Database\Eloquent\Collection 
     */
    public function allWithTrashed(array $relations = []) {
        return Customer::onlyOwners()->with($relations)->withTrashed()->get();
        //return Customer::onlyOwners()->with('certificates', 'certificates.agency')->withTrashed()->get();
    }

    /**
     * Get an customer for a company from its id
     *
     * @param int   $id
     * @param array $relations
     * @param bool  $fail
     *
     * @throws \Scubawhere\Exceptions\Http\HttpNotFound
     *
     * @return \ScubaWhere\Entities\Customer
     */
    public function get($id, array $relations = [], $fail = true) {
        $customer = Customer::onlyOwners()->with($relations)->find($id);

        if(is_null($customer) && $fail) {
            throw new HttpNotFound(__CLASS__. __METHOD__, ['The customer could not be found']);
        }

        return $customer;
    }

    /**
     * Get an customer for a company by a specified column and value
     *
     * @param array $query
     * @param array $relations
     * @param bool  $fail
     *
     * @throws \Scubawhere\Exceptions\Http\HttpNotFound
     *
     * @return \Illuminate\Database\Eloquent\Collection
     */
    public function getWhere(array $query, array $relations = [], $fail = true) {
        $customer = Customer::onlyOwners()->where($query)->with($relations)->find();

        if(is_null($customer) && $fail) {
            throw new HttpNotFound(__CLASS__. __METHOD__, ['The customers could not be found']);
        }

        return $customer;
    }

    /**
     * Get all customers that have an email address
     *
     * @return \Illuminate\Database\Eloquent\Collection
     */
    public function getAllWithEmails() 
    {
        return Customer::onlyOwners()
            ->whereNotNull('email')
            ->where('email', '!=', '')
            ->get();    
    }

    /**
     * Get all customers with specified certification levels
     *
     * @param array $certificate_ids
     *
     * @return \Illuminate\Database\Eloquent\Collection
     */
    public function getCustomersByCertification(array $certificate_ids)
    {
        return Customer::onlyOwners()
            ->whereHas('certificates', function ($query) use ($certificate_ids) {
                $query->whereIn('certificates.id', $certificate_ids);
            })
            ->whereNotNull('email')
            ->where('email', '!=', '')
            ->with('crmSubscription')
            ->get();
    }

    /**
     * Get all customers that belong to speicified bookings
     *
     * @param array $booking_ids
     *
     * @return \Illuminate\Database\Eloquent\Collection
     */
    public function getCustomersByBookings(array $booking_ids)
    {
        return Customer::onlyOwners()
            ->whereIn('id', $booking_ids)
            ->whereNotNull('email')
            ->where('email', '!=', '')
            ->with('crmSubscription')
            ->get(); 
    }

    /**
     * Get all customers that met the filter parameters.
     *
     * This is used as a search functionality for RMS
     *
     * @param  string $firstname
     * @param  string $lastname
     * @param  string $email
     * @param  int    $from Optional parameter for number of customers to skip
     * @param  int    $take Optional parameter for number of customers to return
     *
     * @return \Illuminate\Database\Eloquent\Collection
     */
    public function filter($firstname, $lastname, $email, $from, $take) 
    {
        if(empty($firstname) && empty($lastname) && empty($email)) {
            return $this->getAll();
        }

        $customers = Customer::onlyOwners()
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
     *
     * @param array $data Information about the customer to save
     *
     * @throws \Scubawhere\Exceptions\InvalidInputException
     *
     * @return \ScubaWhere\Entities\Customer
     */
    public function create($data) {
        $customer = new Customer($data);

        if (!$customer->validate()) {
            throw new InvalidInputException($customer->errors()->all());
        }

        return $this->company_model->customers()->save($customer);
    }

    /**
     * Associate a customer with a certification (or many)
     *
     * @param \Scubawhere\Entities\Customer $customer
     * @param array                         $certificates
     * 
     * @throws \Scubawhere\Exceptions\InvalidInputException
     *
     * @return \ScubaWhere\Entities\Customer
     */
    public function associateCertificates(Customer $customer, $certificates)
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
     * Update a customer
     *
     * @param int   $id   ID of the addon
     * @param array $data Information about the addon to update
     * @param bool  $fail Whether to fail or not
     *
     * @throws \Scubawhere\Exceptions\Http\HttpNotAcceptable
     *
     * @return \ScubaWhere\Entities\Customer
     */
    public function update($id, array $data, $fail = true) {
        $customer = $this->get($id, $fail);

        if(!$customer->update($data)) {
            throw new HttpNotAcceptable(__CLASS__ . __METHOD__, [$customer->errors()->all()]);
        }

        return $customer;
    }

    public function delete($id)
    {
        try {
            Context::get()->customers()->where('id', $id)->delete();
        } catch (QueryException $e) {
            // @todo I feel that this should really be a HTTPPreconditionFailed
            throw new HttpUnprocessableEntity(__CLASS__.__METHOD__, ['The customer cannot be deleted, they are assigned to a booking']);
        }
    }

}
