<?php 

namespace Scubawhere\Repositories;

use Scubawhere\Context;
use Scubawhere\Entities\Price;
use Scubawhere\Exceptions\Http\HttpNotFound;
use Scubawhere\Exceptions\MethodNotSupportedException;
use Scubawhere\Exceptions\InvalidInputException;

/**
 * Class PriceRepo acts as a DAO for the accommodation models.
 *
 * It should always return eloquent models and can be thought of as a collection. If at any point in the project, you
 * find yourself accessing the model to retrieve the same data, please add a function to this class and use that.
 *
 * @package Scubawhere\Repositories
 *
 * @see \Scubawhere\Repositories\BaseRepo
 * @see \Scubawhere\Repositories\PriceRepoInterface
 */
class PriceRepo extends BaseRepo implements PriceRepoInterface {

    /** 
     * Eloquent model that acts as the root model to associate assets to
     *
     * @var \Scubawhere\Entities\Company
     */ 
    protected $company_model;
    
    public function __construct() {
        $this->company_model = Context::get();
    }

    /**
     * @throws \Scubawhere\Exceptions\MethodNotSupportedException
     */
    public function all(array $relations = []) {
        throw new MethodNotSupportedException(['error']);
    }

    /**
     * @throws \Scubawhere\Exceptions\MethodNotSupportedException
     */
    public function allWithTrashed(array $relations = []) {
        throw new MethodNotSupportedException(['error']);
    }

    /**
     * Get a price from its id
     *
     * @param int   $id
     * @param array $relations
     * @param bool  $fail
     *
     * @throws \Scubawhere\Exceptions\Http\HttpNotFound
     *
     * @return \Scubawhere\Entities\Price
     */
    public function get($id, array $relations = [], $fail = true) {
        $price = $this->company_model->prices()->with($relations)->find($id);

        if($price === null && $fail) {
            throw new HttpNotFound(__CLASS__ . __METHOD__, ['The price could not be found']);
        }
        
        return $price;
    }

    /**
     * Get a price by a specified column and value
     *
     * @param array $query
     * @param array $relations
     * @param bool  $fail
     *
     * @throws \Scubawhere\Exceptions\Http\HttpNotFound
     *
     * @return \Scubawhere\Entities\Price
     */
    public function getWhere(array $query, array $relations = [], $fail = true) {
        $price = $this->company_model->prices()->with($relations)->find();

        if($price === null && $fail) {
            throw new HttpNotFound(__CLASS__ . __METHOD__, ['The price could not be found']);
        }

        return $price;
    }

    /**
     * Create a price but do not save it
     *
     * @param array $data
     *
     * @throws \Scubawhere\Exceptions\InvalidInputException
     *
     * @return \Scubawhere\Entities\Price
     */
    public function create($data) {
        $price = new Price($data);

        if (!$price->validate()) {
            throw new InvalidInputException($price->errors()->all());
        }

        return $price;
    }

}

