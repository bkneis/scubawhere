<?php 

namespace ScubaWhere\Repositories;

use ScubaWhere\Context;
use ScubaWhere\Exceptions;
use ScubaWhere\Repositories\BaseRepo;
use ScubaWhere\Repositories\AccommodationRepoInterface;
use Illuminate\Database\Eloquent\ModelNotFoundException;

class AccommodationRepo extends BaseRepo implements AccommodationRepoInterface {

    /** 
     * Eloquent model that acts as the root model to associate assets to
     * @var \Company 
    */ 
    protected $company_model;
    
    public function __construct() {
        $this->company_model = Context::get();
    }

    /**
     * Get all accommodations for a company
     * @return \Illuminate\Database\Eloquent\Collection
     */
    public function all() {
        return \Accommodation::onlyOwners()->with('basePrices', 'prices')->get();
    }

    /**
     * Get all accommodations for a company including soft deleted models
     * @return \Illuminate\Database\Eloquent\Collection
     */
    public function allWithTrashed() {
        return \Accommodation::onlyOwners()->with('basePrices', 'prices')->withTrashed()->get();
    }

    /**
     * Get an accommodation for a company from its id
     * @param  int   ID of the accommodation
     * @throws \Illuminate\Database\Eloquent\ModelNotFound
     * @return \Accommodation
     */
    public function get($id) {
        return \Accommodation::onlyOwners()->with('basePrices', 'prices')->findOrFail($id);
    }

    /**
     * Get an accommodation for a company by a specified column and value
     * @param  string Column name to search by
     * @param  mixed  Value to match the accommodation
     * @return \Accommodation
     */
    public function getWhere($column, $value) {
        return \Accommodation::onlyOwners()->where($column, '=', $value)->with('basePrices', 'prices')->get();
    }

    /**
     * Get an accommodation for a company with specified relationships
     * @param  int    ID of the accommodation
     * @param  array  Relationships to retrieve with the model
     * @return \Accommodation
     */
    public function getWith($id, $relations) {
        return \Accommodation::onlyOwners()->with($relations)->findOrFail($id);
    }

    /**
     * Create an accommodation and associate it with its company
     * @param array Information about the accommodation to save
     * @throws \ScubaWhere\Exceptions\InvalidInputException
     * @return \Accommodation
     */
    public function create($data) {
        $accommodation = new \Accommodation($data);
        if (!$accommodation->validate()) {
            throw new InvalidInputException($accommodation->errors()->all());
        }
        return $this->company_model->accommodations()->save($accommodation);
    }

    /**
     * Update an accommodation by id with specified data
     * @param  int   ID of the accommodation
     * @param  array Data to update the accommodation with
     * @throws \ScubaWhere\Exceptions\InvalidInputException
     * @return \Accommodation
     */
    public function update($id, $data) {
        $accommodation = $this->get($id);
        if(!$accommodation->update($data)) {
            throw new InvalidInputException($accommodation->errors()->all());
        }
        return $accommodation;
    }

    /**
     * Delete an accommodation by its id
     * @param  int ID of the accommodation
     * @throws \Exception
     */
    public function delete($id) {
        $accommodation = $this->get($id);
        $accommodation->delete();
    }

    /**
     * Delete an accommodation by a specified column and value
     * @param  string Column name to search by
     * @param  mixed  Value to match the accommodation
     * @throws \Exception
     */
    public function deleteWhere($column, $value) {
        $accommodation = $this->getWhere($column, $value);
        $accommodation->delete();
    }
}