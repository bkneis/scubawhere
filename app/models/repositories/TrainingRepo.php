<?php 

namespace ScubaWhere\Repositories;

use ScubaWhere\Context;
use ScubaWhere\Exceptions;
use ScubaWhere\Repositories\TrainingRepoInterface;
use ScubaWhere\Exceptions\InvalidInputException;
use Illuminate\Database\Eloquent\ModelNotFoundException;

class TrainingRepo extends BaseRepo implements TrainingRepoInterface {

    /** 
     * Eloquent model that acts as the root model to associate assets to
     * \Company 
    */ 
    protected $company_model;
    
    public function __construct() {
        $this->company_model = Context::get();
    }

    /**
     * Get all trainings for a company
     * @return \Illuminate\Database\Eloquent\Collection Eloquent collection with all trainings for a company
     */
    public function all() {
        return \Training::onlyOwners()->get();
    }

    /**
     * Get all trainings for a company including soft deleted models
     * @return \Illuminate\Database\Eloquent\Collection 
     */
    public function allWithTrashed() {
        return \Training::onlyOwners()->withTrashed()->get();
    }

    /**
     * Get an training for a company from its id
     * @param  int   ID of the training
     * @throws \Illuminate\Database\Eloquent\ModelNotFound
     * @return \Training
     */
    public function get($id) {
        return \Training::onlyOwners()->findOrFail($id);
    }

    /**
     * Get an training for a company by a specified column and value
     * @param  string Column name to search by
     * @param  mixed  Value to match the training
     * @return \Training
     */
    public function getWhere($column, $value) {
        return \Training::onlyOwners()->where($column, $value)->get();
    }

    /**
     * Get an training for a company with specified relationships
     * @param  int    ID of the training
     * @param  array  Relationships to retrieve with the model
     * @return \Training
     */
    public function getWith($id, $relations) {
        return \Training::onlyOwners()->with($relations)->findOrFail($id);
    }

    /**
     * Create an training and associate it with its company
     * @param array Information about the training to save
     * @throws \ScubaWhere\Exceptions\InvalidInputException
     * @return \Training
     */
    public function create($data) {
        $training = new \Training($data);
        if (!$training->validate()) {
            throw new InvalidInputException($training->errors()->all());
        }
        return Context::get()->trainings()->save($training);
    }

    /**
     * Update an training by id with specified data
     * @param  int   ID of the training
     * @param  array Data to update the training with
     * @throws \ScubaWhere\Exceptions\InvalidInputException
     * @return \Training
     */
    public function update($id, $data) {
        $training = $this->get($id);
        if(!$training->update($data)) {
            throw new InvalidInputException($training->errors()->all());
        }
        return $training;
    }

    /**
     * Delete an training by its id
     * @param  int ID of the training
     * @throws Exception
     */
    public function delete($id) {
        $training = $this->get($id);
        $training->delete();
    }

    /**
     * Delete an training by a specified column and value
     * @param  string Column name to search by
     * @param  mixed  Value to match the training
     * @throws Exception
     */
    public function deleteWhere($column, $value) {
        $training = $this->getWhere($column, $value);
        $training->delete();
    }
}