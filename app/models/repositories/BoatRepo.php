<?php 

namespace ScubaWhere\Repositories;

use ScubaWhere\Context;
use ScubaWhere\Exceptions;
use ScubaWhere\Repositories\BoatRepoInterface;
use ScubaWhere\Exceptions\InvalidInputException;
use Illuminate\Database\Eloquent\ModelNotFoundException;

class BoatRepo extends BaseRepo implements BoatRepoInterface {

    /** 
     * Eloquent model that acts as the root model to associate assets to
     * @var \Company 
    */ 
    protected $company_model;

    public function __construct() {
        $this->company_model = Context::get();
    }

    /**
     * Get all boats for a company
     * @return \Illuminate\Database\Eloquent\Collection
     */
    public function all() {
        return \Boat::onlyOwners()->with('boatrooms')->get();
    }

    /**
     * Get all boats for a company including soft deleted models
     * @return \Illuminate\Database\Eloquent\Collection
     */
    public function allWithTrashed() {
        return \Boat::onlyOwners()->with('boatrooms')->withTrashed()->get();
    }

    /**
     * Get an boat for a company from its id
     * @param  int   ID of the boat
     * @throws \Illuminate\Database\Eloquent\ModelNotFound
     * @return \Boat
     */
    public function get($id) {
        return \Boat::onlyOwners()->with('boatrooms')->findOrFail($id);
    }

    /**
     * Get an boat for a company by a specified column and value
     * @param  string Column name to search by
     * @param  mixed  Value to match the boat
     * @return \Boat
     */
    public function getWhere($column, $value) {
        return \Boat::onlyOwners()->where($column, $value)->with('boatrooms')->get();
    }

    /**
     * Get an boat for a company with specified relationships
     * @param  int    ID of the accommodation
     * @param  array  Relationships to retrieve with the model
     * @return \Boat
     */
    public function getWith($id, $relations) {
        return \Boat::onlyOwners()->with($relations)->findOrFail($id);
    }

    /**
     * Create an boat and associate it with its company
     * @param array Information about the boat to save
     * @throws \ScubaWhere\Exceptions\InvalidInputException
     * @return \Boat
     */
    public function create($data) {
        $boat = new \Boat($data);
        if (!$boat->validate()) {
            throw new InvalidInputException($boat->errors()->all());
        }
        return $this->company_model->boats()->save($boat);
    }

    /**
     * Update an boat by id with specified data
     * @param  int   ID of the boat
     * @param  array Data to update the boat with
     * @throws \ScubaWhere\Exceptions\InvalidInputException
     * @return \Boat
     */
    public function update($id, $data) {
        $boat = $this->get($id);
        if(!$boat->update($data)) {
            throw new InvalidInputException($boat->errors()->all());
        }
        return $boat;
    }

    /**
     * Delete an boat by its id
     * @param  int ID of the boat
     * @throws Exception
     */
    public function delete($id) {
        $boat = $this->get($id);
        $boat->delete();
    }

    /**
     * Delete an boat by a specified column and value
     * @param  string Column name to search by
     * @param  mixed  Value to match the boat
     * @throws Exception
     */
    public function deleteWhere($column, $value) {
        $boat = $this->getWhere($column, $value);
        $boat->delete();
    }
}