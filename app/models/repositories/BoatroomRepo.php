<?php 

namespace ScubaWhere\Repositories;

use ScubaWhere\Context;
use ScubaWhere\Exceptions;
use ScubaWhere\Exceptions\InvalidInputException;
use ScubaWhere\Repositories\BoatroomRepoInterface;
use Illuminate\Database\Eloquent\ModelNotFoundException;

class BoatroomRepo implements BoatroomRepoInterface {

    /** 
     * Eloquent model that acts as the root model to associate assets to
     * @var \Company 
    */ 
    protected $company_model;
    
    public function __construct() {
        $this->company_model = Context::get();
    }

    /**
     * Get all boatrooms for a company
     * @return \Illuminate\Database\Eloquent\Collection Eloquent collection with all boatrooms for a company
     */
    public function all() {
        return \Boatroom::onlyOwners()->get();
    }

    /**
     * Get all boatrooms for a company including soft deleted models
     * @return \Illuminate\Database\Eloquent\Collection Eloquent collection with all boatrooms for a company including soft deleted models
     */
    public function allWithTrashed() {
        return \Boatroom::onlyOwners()->withTrashed()->get();
    }

    /**
     * Get a boatroom for a company from its id
     * @param  int   ID of the boatroom
     * @throws \Illuminate\Database\Eloquent\ModelNotFound
     * @return \Illuminate\Database\Eloquent\Model Eloquent model of an boatroom for a company
     */
    public function get($id) {
        return \Boatroom::onlyOwners()->findOrFail($id);
    }

    /**
     * Get a boatroom for a company by a specified column and value
     * @param  string Column name to search by
     * @param  mixed  Value to match the boatroom
     * @return \Illuminate\Database\Eloquent\Model Eloquent model of an boatroom for a company
     */
    public function getWhere($column, $value) {
        return \Boatroom::onlyOwners()->where($column, $value)->get();
    }

    /**
     * Create a boatroom and associate it with its company
     * @param array Information about the boatroom to save
     * @throws \ScubaWhere\Exceptions\InvalidInputException
     * @return \Illuminate\Database\Eloquent\Model Eloquent model of an boatroom for a company
     */
    public function create($data) {
        $boatroom = new \Boatroom($data);
        if (!$boatroom->validate()) {
            throw new InvalidInputException($boatroom->errors()->all());
        }
        return $this->company_model->boatrooms()->save($boatroom);
    }

    /**
     * Update an boatroom by id with specified data
     * @param  int   ID of the boatroom
     * @param  array Data to update the boatroom with
     * @throws \ScubaWhere\Exceptions\InvalidInputException
     * @return \Illuminate\Database\Eloquent\Model Eloquent model of an boatroom for a company
     */
    public function update($id, $data) {
        $boatroom = $this->get($id);
        if(!$boatroom->update($data)) {
            throw new InvalidInputException($boatroom->errors()->all());
        }
        return $boatroom;
    }

    /**
     * Delete an boatroom by its id
     * @param  int ID of the boatroom
     * @throws Exception
     */
    public function delete($id) {
        $boatroom = $this->get($id);
        $boatroom->delete();
    }

    /**
     * Delete an boatroom by a specified column and value
     * @param  string Column name to search by
     * @param  mixed  Value to match the boatroom
     * @throws Exception
     */
    public function deleteWhere($column, $value) {
        $boatroom = $this->getWhere($column, $value);
        $boatroom->delete();
    }
}