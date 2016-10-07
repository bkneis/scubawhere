<?php 

namespace ScubaWhere\Repositories;

use ScubaWhere\Context;
use ScubaWhere\Exceptions;
use ScubaWhere\Repositories\LocationRepoInterface;
use Illuminate\Database\Eloquent\ModelNotFoundException;

class LocationRepo implements LocationRepoInterface {

    /** 
     * Eloquent model that acts as the root model to associate assets to
     * \Company 
    */ 
    protected $company_model;
    
    public function __construct() {
        $this->company_model = Context::get();
    }

    /**
     * Get all locations for a company
     * @return \Illuminate\Database\Eloquent\Collection Eloquent collection with all locations for a company
     */
    public function all() {
        return $this->company_model->locations()->with('tags')->get();
    }

    /**
     * Get all locations for a company including soft deleted models
     * @return \Illuminate\Database\Eloquent\Collection Eloquent collection with all locations for a company including soft deleted models
     */
    public function allWithTrashed() {
        return $this->company_model->locations()->with('tags')->withTrashed()->get();
    }

    /**
     * Get an location for a company from its id
     * @param  int   ID of the location
     * @throws \Illuminate\Database\Eloquent\ModelNotFound
     * @return \Illuminate\Database\Eloquent\Model Eloquent model of an location for a company
     */
    public function get($id) {
        return \Location::with('tags')->findOrFail($id);
        //return $this->company_model->locations()->with('tags')->findOrFail($id);
    }

    /**
     * Get an location for a company by a specified column and value
     * @param  string Column name to search by
     * @param  mixed  Value to match the location
     * @return \Illuminate\Database\Eloquent\Model Eloquent model of an location for a company
     */
    public function getWhere($column, $value) {
        return $this->company_model->locations()->where($column, $value)->with('tags')->get();
    }

    /**
     * Create an location and associate it with its company
     * @param array Information about the location to save
     * @throws \ScubaWhere\Exceptions\InvalidInputException
     * @return \Illuminate\Database\Eloquent\Model Eloquent model of an location for a company
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
     * @return \Illuminate\Database\Eloquent\Model Eloquent model of an location for a company
     */
    public function update($id, $description) {
        $location = $this->get($id);
        Context::get()->locations()->updateExistingPivot($location->id, ['description' => $description]);
        return $location;
    }

    /**
     * Delete an location by its id
     * @param  int ID of the location
     * @throws Exception
     */
    public function delete($id) {
        $location = $this->get($id);
        $location->delete();
    }

    /**
     * Delete an location by a specified column and value
     * @param  string Column name to search by
     * @param  mixed  Value to match the location
     * @throws Exception
     */
    public function deleteWhere($column, $value) {
        $location = $this->getWhere($column, $value);
        $location->delete();
    }
}