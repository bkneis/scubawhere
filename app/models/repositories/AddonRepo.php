<?php 

namespace ScubaWhere\Repositories;

use ScubaWhere\Context;
use ScubaWhere\Exceptions;
use ScubaWhere\Exceptions\InvalidInputException;
use ScubaWhere\Repositories\AddonRepoInterface;
use Illuminate\Database\Eloquent\ModelNotFoundException;

class AddonRepo extends BaseRepo implements AddonRepoInterface {

    /** 
     * Eloquent model that acts as the root model to associate assets to
     * @var \Company 
     */ 
    protected $company_model;
    
    public function __construct() {
        $this->company_model = Context::get();
    }

    /**
     * Get all addons for a company
     * @return \Illuminate\Database\Eloquent\Collection Eloquent collection with all addons for a company
     */
    public function all() {
        return \Addon::where('company_id', '=', $this->company_model->id)->with('basePrices')->get();
    }

    /**
     * Get all addons for a company including soft deleted models
     * @return \Illuminate\Database\Eloquent\Collection Eloquent collection with all addons for a company including soft deleted models
     */
    public function allWithTrashed() {
        return \Addon::where('company_id', '=', $this->company_model->id)->with('basePrices')->withTrashed()->get();
    }

    /**
     * Get an addon for a company from its id
     * @param  int ID of the addon
     * @throws \Illuminate\Database\Eloquent\ModelNotFound
     * @return \Addon
     */
    public function get($id) {
        return \Addon::with('basePrices')->findOrFail($id);
    }

    /**
     * Get an addon for a company by a specified column and value
     * @param  string Column name to search by
     * @param  mixed  Value to match the addon
     * @return \Addon
     */
    public function getWhere($column, $value) {
        return \Addon::where($column, '=', $value)->with('basePrices')->get();
    }

    /**
     * Get an addon for a company with specified relationships
     * @param  int    ID of the addon
     * @param  array  Relationships to retrieve with the model
     * @return \Addon
     */
    public function getWith($id, $relations) {
        return \Accommodation::onlyOwners()->with($relations)->findOrFail($id);
    }

    /**
     * Create an addon and associate it with its company
     * @param array Information about the addon to save
     * @throws \ScubaWhere\Exceptions\InvalidInputException
     * @return \Addon
     */
    public function create($data) {
        $addon = new \Addon($data);
        if (!$addon->validate()) {
            throw new InvalidInputException($addon->errors()->all());
        }
        return Context::get()->addons()->save($addon);
    }

    /**
     * Update an addon by id with specified data
     * @param  int   ID of the addon
     * @param  array Data to update the addon with
     * @throws \ScubaWhere\Exceptions\InvalidInputException
     * @return \Addon
     */
    public function update($id, $data) {
        $addon = $this->get($id);
        if(!$addon->update($data)) {
            throw new InvalidInputException($addon->errors()->all());
        }
        return $addon;
    }

    /**
     * Delete an addon by its id
     * @param  int ID of the addon
     * @throws \Exception
     */
    public function delete($id) {
        $addon = $this->get($id);
        $addon->delete();
    }

    /**
     * Delete an addon by a specified column and value
     * @param  string Column name to search by
     * @param  mixed  Value to match the addon
     * @throws \Exception
     */
    public function deleteWhere($column, $value) {
        $addon = $this->getWhere($column, $value);
        $addon->delete();
    }
}