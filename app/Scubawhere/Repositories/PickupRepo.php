<?php 

namespace Scubawhere\Repositories;

use Scubawhere\Context;
use Scubawhere\Entities\PickUp;
use Scubawhere\Exceptions\Http\HttpNotFound;
use Scubawhere\Exceptions\MethodNotSupportedException;
use Scubawhere\Exceptions\InvalidInputException;

/**
 * Class PickupRepo acts as a DAO for the accommodation models.
 *
 * It should always return eloquent models and can be thought of as a collection. If at any point in the project, you
 * find yourself accessing the model to retrieve the same data, please add a function to this class and use that.
 *
 * @package Scubawhere\Repositories
 *
 * @see \Scubawhere\Repositories\BaseRepo
 * @see \Scubawhere\Repositories\PickupRepoInterface
 */
class PickupRepo extends BaseRepo implements PickupRepoInterface {

    /** 
     * Eloquent model that acts as the root model to associate assets to
     *
     * @var \Scubawhere\Entities\Company
     */ 
    protected $company_model;
    
    public function __construct() {
        $this->company_model = Context::get();
    }

    /**
	 * @throws \Scubawhere\Exceptions\MethodNotSupportedException
     */
    public function all(array $relations = []) {
		throw new MethodNotSupportedException(['error']);
    }

    /**
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
     * @return \Scubawhere\Entities\PickUp
     */
    public function get($id, array $relations = [], $fail = true) {
        $pickup = $this->company_model->pickups()->with($relations)->find($id);

        if(is_null($pickup) && $fail) {
            throw new HttpNotFound(__CLASS__ . __METHOD__, ['The customer pick up information could not be found']);
        }

        return $pickup;
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
     * @return \Scubawhere\Entities\PickUp
     */
    public function getWhere(array $query, array $relations = [], $fail = true) {
        $pickup = $this->company_model->pickups()->where($query)->with($relations)->find();

        if(is_null($pickup) && $fail) {
            throw new HttpNotFound(__CLASS__ . __METHOD__, ['The customer pick up information could not be found']);
        }

        return $pickup;
    }

    /**
     * Create an addon and associate it with its company
     *
     * @param array Information about the addon to save
     *
     * @throws \Scubawhere\Exceptions\InvalidInputException
     *
     * @return \Scubawhere\Entities\PickUp
     */
    public function create($data) {
        $pick_up = new PickUp($data);

        if (!$pick_up->validate()) {
			throw new InvalidInputException($pick_up->errors()->all());
        }

		return $pick_up;
    }

}

