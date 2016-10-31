<?php 

namespace Scubawhere\Repositories;

use Scubawhere\Context;
use Scubawhere\Exceptions;
use Scubawhere\Entities\Departure;
use Scubawhere\Exceptions\Http\HttpNotFound;
use Scubawhere\Exceptions\Http\HttpNotAcceptable;
use Scubawhere\Exceptions\InvalidInputException;

/**
 * Class DepartureRepo acts as a DAO for the accommodation models.
 *
 * It should always return eloquent models and can be thought of as a collection. If at any point in the project, you
 * find yourself accessing the model to retrieve the same data, please add a function to this class and use that.
 *
 * @package Scubawhere\Repositories
 *
 * @see \Scubawhere\Repositories\BaseRepo
 * @see \Scubawhere\Repositories\DepartureRepoInterface
 */
class DepartureRepo extends BaseRepo implements DepartureRepoInterface {

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
     * Get all departures for a company
	 *
	 * @param array $relations
	 *
     * @return \Illuminate\Database\Eloquent\Collection
     */
	public function all(array $relations = [])
	{
		return $this->company_model
			->departures()
			->with($relations)
			->all(['sessions.*']);
    }

    /**
     * Get all departures for a company including soft deleted models
	 *
	 * @param array $relations
	 *
     * @return \Illuminate\Database\Eloquent\Collection 
     */
	public function allWithTrashed(array $relations = [])
	{
		return $this->company_model
			->departures()
			->with($relations)
			->withTrashed()
			->all(['sessions.*']);
    }

    /**
     * Get an departure for a company from its id
	 *
     * @param int   $id
	 * @param array $relations
	 * @param bool  $fail
	 *
     * @throws \Scubawhere\Exceptions\Http\HttpNotFound
	 *
     * @return \Scubawhere\Entities\Departure
     */
	public function get($id, array $relations = [], $fail = true)
	{
		$departure = $this->company_model
			->departures()
			->where('sessions.id', $id)
			->with($relations)
			->firstOrFail(['sessions.*']);

		if(is_null($departure) && $fail) {
			throw new HttpNotFound(__CLASS__ . __METHOD__, ['The departure could not be found']);
		}

		return $departure;
    }

    /**
     * Get an departure for a company by a specified column and value
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
		$departure = $this->company_model
			->departures()
			->where($query)
			->with($relations)
			->firstOrFail(['sessions.*']);

		if(is_null($departure) && $fail) {
			throw new HttpNotFound(__CLASS__ . __METHOD__, ['The departure could not be found']);
		}

		return $departure;
    }

    /**
     * Create an departure and associate it with its company
	 *
     * @param array $data Information about the departure to save
	 *
     * @throws \Scubawhere\Exceptions\InvalidInputException
	 *
     * @return \Scubawhere\Entities\Departure
     */
    public function create($data) {
        $departure = new Departure($data);

        if (!$departure->validate()) {
            throw new InvalidInputException($departure->errors()->all());
        }

        return $this->company_model->departures()->save($departure);
    }

	/**
	 * Update an departure
	 *
	 * @param int   $id   ID of the departue
	 * @param array $data Information about the addon to update
	 * @param bool  $fail Whether to fail or not
	 *
	 * @throws \Scubawhere\Exceptions\Http\HttpNotAcceptable
	 *
	 * @return \ScubaWhere\Entities\Departure
	 */
	public function update($id, array $data, $fail = true) {
		$departure = $this->get($id, $fail);

		if(!$departure->update($data)) {
			throw new HttpNotAcceptable(__CLASS__ . __METHOD__, [$departure->errors()->all()]);
		}

		return $departure;
	}

}

