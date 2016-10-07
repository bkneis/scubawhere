<?php 

namespace ScubaWhere\Repositories;

use ScubaWhere\Context;
use ScubaWhere\Exceptions;
use ScubaWhere\Repositories\LocationRepoInterface;
use Illuminate\Database\Eloquent\ModelNotFoundException;

class LocationRepo extends BaseRepo implements LocationRepoInterface {

    /** 
     * Eloquent model that acts as the root model to associate assets to
     * @var \Company 
    */ 
    protected $company_model;
    
    public function __construct() {
        $this->company_model = Context::get();
    }

    /**
     * Get all locations for a company
     * @return \Illuminate\Database\Eloquent\Collection
     */
    public function all() {
        return $this->company_model->locations()->with('tags')->get();
    }

    /**
     * Get all locations for a company including soft deleted models
     * @return \Illuminate\Database\Eloquent\Collection
     */
    public function allWithTrashed() {
        return $this->company_model->locations()->with('tags')->withTrashed()->get();
    }

    /**
     * Get an location for a company from its id
     * @param  int   ID of the location
     * @throws \Illuminate\Database\Eloquent\ModelNotFound
     * @return \Location
     * @return \Illuminate\Database\Eloquent\Model 
     */
    public function get($id) {
        return \Location::with('tags')->findOrFail($id);
    }

    /**
     * Get an location for a company by a specified column and value
     * @param  string Column name to search by
     * @param  mixed  Value to match the location
     * @return \Location
     */
    public function getWhere($column, $value) {
        return $this->company_model->locations()->where($column, $value)->with('tags')->get();
    }

    /**
     * Get an accommodation for a company with specified relationships
     * @param  int    ID of the accommodation
     * @param  array  Relationships to retrieve with the model
     * @return \Location
     */
    public function getWith($id, $relations) {
        return $this->company_model->locations()->with($relations)->findOrFail($id);
    }

    /**
     * Create an location and associate it with its company
     * @param array Information about the location to save
     * @throws \ScubaWhere\Exceptions\InvalidInputException
     * @return \Location
     */
    public function create($data) {
        $location = new \Location($data);
        if (!$location->validate()) {
            throw new InvalidInputException($location->errors()->all());
        }
        $location->save();
        Context::get()->locations()->attach($location->id);
        return $location;
    }

    /**
     * Update an location by id with specified data
     * @param  int   ID of the location
     * @param  array Data to update the location with
     * @throws \ScubaWhere\Exceptions\InvalidInputException
     * @return \Location
     */
    public function update($id, $description) {
        $location = $this->get($id);
        Context::get()->locations()->updateExistingPivot($location->id, ['description' => $description]);
        return $location;
    }

    /**
     * Delete an location by its id
     * @param  int ID of the location
     * @throws \Exception
     */
    public function delete($id) {
        $location = $this->get($id);
        $location->delete();
    }

    /**
     * Delete an location by a specified column and value
     * @param  string Column name to search by
     * @param  mixed  Value to match the location
     * @throws \Exception
     */
    public function deleteWhere($column, $value) {
        $location = $this->getWhere($column, $value);
        $location->delete();
    }
}