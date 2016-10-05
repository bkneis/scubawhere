<?php 

namespace ScubaWhere\Repositories;

use ScubaWhere\Context;
use ScubaWhere\Exceptions\MethodNotSupportedException;
use ScubaWhere\Repositories\PriceRepoInterface;

class PriceRepo implements PriceRepoInterface {

    /** 
     * Eloquent model that acts as the root model to associate assets to
     * \Company 
     */ 
    protected $company_model;
    
    public function __construct() {
        $this->company_model = Context::get();
    }

    /**
     * @throws \ScubaWhere\Exceptions\MethodNotSupportedException
     */
    public function all() {
        throw new MethodNotSupportedException();
    }

    /**
     * @throws \ScubaWhere\Exceptions\MethodNotSupportedException
     */
    public function allWithTrashed() {
        throw new MethodNotSupportedException();
    }

    /**
     * Get a price from its id
     * @param  int ID of the price
     * @throws \Illuminate\Database\Eloquent\ModelNotFound
     * @return \Illuminate\Database\Eloquent\Model Eloquent model of price
     */
    public function get($id) {
        return \Price::findOrFail($id);
    }

    /**
     * Get a price by a specified column and value
     * @param  string Column name to search by
     * @param  mixed  Value to match the price
     * @return \Illuminate\Database\Eloquent\Model Eloquent model of price
     */
    public function getWhere($column, $value) {
        return \Price::where($column, '=', $value)->get();
    }

    /**
     * Create a price but do not save it
     * @param array Information about the price to create
     * @throws \ScubaWhere\Exceptions\InvalidInputException
     * @return \Illuminate\Database\Eloquent\Model Eloquent model of price
     */
    public function create($data) {
        $price = new \Price($data);
        if (!$price->validate()) {
            throw new InvalidInputException($price->errors()->all());
        }
        return $price;
    }

    /**
     * Update a price by id with specified data
     * @param  int   ID of the price
     * @param  array Data to update the price with
     * @throws \ScubaWhere\Exceptions\InvalidInputException
     * @return \Illuminate\Database\Eloquent\Model Eloquent model of price
     */
    public function update($id, $data) {
        $price = $this->get($id);
        if(!$price->update($data)) {
            throw new InvalidInputException($price->errors()->all());
        }
        return $price;
    }

    /**
     * Delete a price by its id
     * @param  int ID of the addon
     * @throws Exception
     */
    public function delete($id) {
        $price = $this->get($id);
        $price->delete();
    }

    /**
     * Delete a price by a specified column and value
     * @param  string Column name to search by
     * @param  mixed  Value to match the price
     * @throws Exception
     */
    public function deleteWhere($column, $value) {
        $price = $this->getWhere($column, $value);
        $price->delete();
    }
}