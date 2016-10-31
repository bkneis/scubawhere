<?php 

namespace Scubawhere\Repositories;

use Scubawhere\Context;
use Scubawhere\Exceptions;
use Scubawhere\Entities\Trip;
use Scubawhere\Exceptions\Http\HttpNotFound;
use Scubawhere\Exceptions\Http\HttpNotAcceptable;
use Scubawhere\Exceptions\Http\HttpBadRequest;

/**
 * Class TripRepo acts as a DAO for the accommodation models.
 *
 * It should always return eloquent models and can be thought of as a collection. If at any point in the project, you
 * find yourself accessing the model to retrieve the same data, please add a function to this class and use that.
 *
 * @todo Move the functions related to associating relationships to the service layer
 *
 * @package Scubawhere\Repositories
 *
 * @see \Scubawhere\Repositories\BaseRepo
 * @see \Scubawhere\Repositories\TripRepoInterface
 */
class TripRepo extends BaseRepo implements TripRepoInterface {

    /** 
     * Eloquent model that acts as the root model to associate assets to
     *
     * @var \Scubawhere\Entities\Company
    */ 
    protected $company_model;

    /**
     * Database connection to use when accessing DB directly
     *
     * @var \Illuminate\Database\Connection
     */
    protected $db;

    public function __construct() {
        $this->company_model = Context::get();
    }

    /**
     * Get all trips for a company
     *
     * @param array $relations
     *
     * @return \Illuminate\Database\Eloquent\Collection
     */
    public function all(array $relations = []) {
        return Trip::onlyOwners()->with($relations)->get();
    }

    /**
     * Get all trips for a company including soft deleted models
     *
     * @param array $relations
     *
     * @return \Illuminate\Database\Eloquent\Collection
     */
    public function allWithTrashed(array $relations = []) {
        return Trip::onlyOwners()->withTrashed()->with($relations)->get();
    }

    /**
     * Get an trip for a company from its id
     *
     * @param int $id
     *
     * @throws \ScubaWhere\Exceptions\Http\HttpNotFound
     *
     * @return \Scubawhere\Entities\Trip
     */
    public function get($id, array $relations = [], $fail = true) {
        $trip = Trip::onlyOwners()->with($relations)->find($id);

        if(is_null($trip) && $fail) {
            throw new HttpNotFound(__CLASS__.__METHOD__, ['The trip could not be found']);
        }

        return $trip;
    }

    /**
     * Get an trip for a company by a specified column and value
     *
     * @param array $query
     * @param array $relations
     * @param bool  $fail
     *
     * @throws HttpNotFound
     *
     * @return \Scubawhere\Entities\Trip
     */
    public function getWhere(array $query, array $relations = [], $fail = true) {
        $trip = Trip::onlyOwners()->where($query)->with($relations)->get();

        if(is_null($trip) && $fail) {
            throw new HttpNotFound(__CLASS__.__METHOD__, ['The trip could not be found']);
        }

        return $trip;
    }

    /**
     * Associate any locations to a trip
     *
     * @note Should this be in the location service ??
     *
     * @param Trip  $trip
     * @param array $locations
     *
     * @throws \Scubawhere\Exceptions\Http\HttpBadRequest
     *
     * @return void 
     */
    private function associateLocations(Trip $trip, array $locations)
    {
        try {
            $trip->locations()->sync($locations);
        } catch (\Exception $e) {
            throw new HttpBadRequest(__CLASS__.__METHOD__, ['Could not assign locations to trip, \'locations\' array is propably erroneous.']);
        }
    }

    /**
     * Associate any tags to a trip
     *
     * @note Should this be in the tags service ??
     *
     * @param Trip  $trip
     * @param array $tags
     *
     * @throws \Scubawhere\Exceptions\Http\HttpBadRequest
     *
     * @return void 
     */
    private function associateTags(Trip $trip, $tags)
    {
        try {
            $trip->tags()->sync($tags);
        } catch (\Exception $e) {
            throw new HttpBadRequest(__CLASS__.__METHOD__, ['Could not assign locations to trip, \'tags\' array is propably erroneous.']);
        }
    }

    /**
     * Create an trip and associate it with its company
     *
     * @param array $data
     * @param array $locations
     * @param array $tags
     *
     * @throws \Scubawhere\Exceptions\InvalidInputException
     * @throws \Exception
     *
     * @return \Illuminate\Database\Eloquent\Model Eloquent model of an trip for a company
     */
    public function create($data, $locations, $tags) {
        \DB::beginTransaction();
        try 
        {
            $trip = new Trip($data);
            if (!$trip->validate()) {
                throw new HttpNotAcceptable(__CLASS__.__METHOD__, $trip->errors()->all());
            }
            $trip = $this->company_model->trips()->save($trip);
            $this->associateLocations($trip, $locations);
            $this->associateTags($trip, $tags);
            \DB::commit();
        }
        catch(\Exception $e) {
            \DB::rollback();
            throw $e;
        }
        return $trip;
    }

    /**
     * Update an trip by id with specified data
     *
     * @param int   $id
     * @param array $data
     * @param array $locations
     * @param array $tags
     *
     * @throws \Scubawhere\Exceptions\InvalidInputException
     * @throws \Exception
     *
     * @return \Illuminate\Database\Eloquent\Model Eloquent model of an trip for a company
     */
    public function update($id, $data, $locations, $tags) {
        \DB::beginTransaction();
        try 
        {
            $trip = $this->get($id);
            if(!$trip->update($data)) {
                throw new HttpNotAcceptable(__CLASS__.__METHOD__, $trip->errors()->all());
            }
            $trip = $this->company_model->trips()->save($trip);
            $this->associateLocations($trip, $locations);
            $this->associateTags($trip, $tags);
            \DB::commit();
        }
        catch(\Exception $e) {
            \DB::rollback();
            throw $e;
        }
        return $trip;
    }

}
