<?php 

namespace ScubaWhere\Repositories;

use ScubaWhere\Context;
use ScubaWhere\Exceptions;
use ScubaWhere\Exceptions\InvalidInputException;
use ScubaWhere\Repositories\TripRepoInterface;
use Illuminate\Database\Eloquent\ModelNotFoundException;

class TripRepo extends BaseRepo implements TripRepoInterface {

    /** 
     * Eloquent model that acts as the root model to associate assets to
     * @var \Company 
    */ 
    protected $company_model;

    /**
     * Database connection to use when accessing DB directly
     * @var \Illuminate\Database\Connection
     */
    protected $db;

    public function __construct() {
        $this->company_model = Context::get();
    }

    /**
     * Get all trips for a company
     * @return \Illuminate\Database\Eloquent\Collection Eloquent collection with all trips for a company
     */
    public function all() {
        return \Trip::onlyOwners()->with('locations', 'tags')->get();
    }

    /**
     * Get all trips for a company including soft deleted models
     * @return \Illuminate\Database\Eloquent\Collection Eloquent collection with all trips for a company including soft deleted models
     */
    public function allWithTrashed() {
        return \Trip::onlyOwners()->withTrashed()->with('locations', 'tags')->get();
    }

    /**
     * Get an trip for a company from its id
     * @param  int   ID of the trip
     * @throws \Illuminate\Database\Eloquent\ModelNotFound
     * @return \Illuminate\Database\Eloquent\Model Eloquent model of an trip for a company
     */
    public function get($id) {
        return \Trip::onlyOwners()->with('locations', 'tags', 'tickets')->findOrFail($id);
    }

    /**
     * Get an trip for a company by a specified column and value
     * @param  string Column name to search by
     * @param  mixed  Value to match the trip
     * @return \Illuminate\Database\Eloquent\Model Eloquent model of an trip for a company
     */
    public function getWhere($column, $value) {
        return \Trip::onlyOwners()->where($column, $value)->with('locations', 'tags', 'tickets')->get();
    }

    /**
     * Get a trip for a company with specified relationships
     * @param  int    ID of the trip
     * @param  array  Relationships to retrieve with the model
     * @return \Trip 
     */
    public function getWith($id, $relations) {
        return \Trip::onlyOwners()->with($relations)->findOrFail($id);
    }

    /**
     * Associate any locations to a trip
     * @note Should this be in the location service ?? 
     * @param  \Location Model to associate tags to
     * @return void 
     */
    private function associateLocations($trip, $locations)
    {
        try {
            $trip->locations()->sync($locations);
        } catch (Exception $e) {
            throw new BadRequestException(['Could not assign locations to trip, \'locations\' array is propably erroneous.']);
        }
    }

    /**
     * Associate any tags to a trip
     * @note Should this be in the tags service ?? 
     * @param  \Trip Model to associate tags to
     * @return void 
     */
    private function associateTags($trip, $tags)
    {
        try {
            $trip->tags()->sync($tags);
        } catch (Exeption $e) {
            throw new BadRequestException(['Could not assign locations to trip, \'tags\' array is propably erroneous.']);
        }
    }

    /**
     * Create an trip and associate it with its company
     * @param array Information about the trip to save
     * @throws \ScubaWhere\Exceptions\InvalidInputException
     * @return \Illuminate\Database\Eloquent\Model Eloquent model of an trip for a company
     */
    public function create($data, $locations, $tags) {
        \DB::beginTransaction();
        try 
        {
            $trip = new \Trip($data);
            if (!$trip->validate()) {
                throw new InvalidInputException($trip->errors()->all());
            }
            $trip = $this->company_model->trips()->save($trip);
            $this->associateLocations($trip, $locations);
            $this->associateTags($trip, $tags);
            \DB::commit();
        }
        catch(Exception $e) {
            \DB::rollback();
            throw $e;
        }
        return $trip;
    }

    /**
     * Update an trip by id with specified data
     * @param  int   ID of the trip
     * @param  array Data to update the trip with
     * @throws \ScubaWhere\Exceptions\InvalidInputException
     * @return \Illuminate\Database\Eloquent\Model Eloquent model of an trip for a company
     */
    public function update($id, $data, $locations, $tags) {
        \DB::beginTransaction();
        try 
        {
            $trip = $this->get($id);
            if(!$trip->update($data)) {
                throw new InvalidInputException($trip->errors()->all());
            }
            $trip = $this->company_model->trips()->save($trip);
            $this->associateLocations($trip, $locations);
            $this->associateTags($trip, $tags);
            \DB::commit();
        }
        catch(Exception $e) {
            \DB::rollback();
            throw $e;
        }
        return $trip;
    }

    /**
     * Delete an trip by its id
     * @param  int ID of the trip
     * @throws Exception
     */
    public function delete($id) {
        $trip = $this->get($id);
        $trip->delete();
    }

    /**
     * Delete an trip by a specified column and value
     * @param  string Column name to search by
     * @param  mixed  Value to match the trip
     * @throws Exception
     */
    public function deleteWhere($column, $value) {
        $trip = $this->getWhere($column, $value);
        $trip->delete();
    }
}