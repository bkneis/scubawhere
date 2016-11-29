<?php 

namespace Scubawhere\Repositories;

use Scubawhere\Context;
use Scubawhere\Exceptions;
use Scubawhere\Entities\Payment;
use Scubawhere\Exceptions\Http\HttpNotFound;
use Scubawhere\Exceptions\InvalidInputException;
use Scubawhere\Exceptions\MethodNotSupportedException;

/**
 * Class PaymentRepo acts as a DAO for the accommodation models.
 *
 * It should always return eloquent models and can be thought of as a collection. If at any point in the project, you
 * find yourself accessing the model to retrieve the same data, please add a function to this class and use that.
 *
 * @package Scubawhere\Repositories
 *
 * @see \Scubawhere\Repositories\BaseRepo
 * @see \Scubawhere\Repositories\PaymentRepoInterface
 */
class PaymentRepo extends BaseRepo implements PaymentRepoInterface {

    /** 
     * Eloquent model that acts as the root model to associate assets to
     *
     * @var \Company 
     */ 
    protected $company_model;
    
    public function __construct() {
        $this->company_model = Context::get();
    }

    /**
     * @param array $relations
     *
     * @throws \Scubawhere\Exceptions\MethodNotSupportedException
     */
    public function all(array $relations = []) {
		throw new MethodNotSupportedException(['error']);
    }

    /**
     * @param array $relations
     *
     * @throws \Scubawhere\Exceptions\MethodNotSupportedException
     */
    public function allWithTrashed(array $relations = []) {
		throw new MethodNotSupportedException(['error']);
    }

    /**
     * Get an addon for a company from its id
     *
     * @param int   $id
     * @param array $relations
     * @param bool  $fail
     *
     * @throws \Scubawhere\Exceptions\Http\HttpNotFound
     *
     * @return \ScubaWhere\Entities\Payment
     */
    public function get($id, array $relations = [], $fail = true) {
        $payment = Payment::with($relations)->find($id);

        if(is_null($payment) && $fail) {
            throw new HttpNotFound(__CLASS__ . __METHOD__, ['The payment could not be found']);
        }

        return $payment;
    }

    /**
     * Get an addon for a company by a specified column and value
	 *
     * @param array $query
     * @param array $relations
     * @param bool  $fail
     *
     * @throws \Scubawhere\Exceptions\Http\HttpNotFound
	 *
     * @return \Scubawhere\Entities\Payment
     */
    public function getWhere(array $query, array $relations = [], $fail = true) {
        $payment = Payment::where($query)->with($relations)->find();

        if(is_null($payment) && $fail) {
            throw new HttpNotFound(__CLASS__ . __METHOD__, ['The payment could not be found']);
        }

        return $payment;
    }

    /**
     * Create an addon and associate it with its company
	 *
     * @param array Information about the addon to save
	 *
     * @throws \Scubawhere\Exceptions\InvalidInputException
	 *
     * @return \Scubawhere\Entities\Payment
     */
    public function create($data) {
        $payment = new Payment($data);
        if (!$payment->validate()) {
            throw new InvalidInputException($payment->errors()->all());
        }
		$payment->save();
        return $payment;
    }

}

