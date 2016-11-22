<?php 

namespace Scubawhere\Repositories;

use Scubawhere\Context;
use Scubawhere\Exceptions\Http\HttpNotFound;
use Scubawhere\Entities\Boatroom;
use Scubawhere\Exceptions\Http\HttpUnprocessableEntity;
use Scubawhere\Exceptions\InvalidInputException;

/**
 * Class BoatroomRepo acts as a DAO for the accommodation models.
 *
 * It should always return eloquent models and can be thought of as a collection. If at any point in the project, you
 * find yourself accessing the model to retrieve the same data, please add a function to this class and use that.
 *
 * @package Scubawhere\Repositories
 *
 * @see \Scubawhere\Repositories\BaseRepo
 * @see \Scubawhere\Repositories\BoatRepoInterface
 */
class BoatroomRepo extends BaseRepo implements BoatroomRepoInterface {

    /** 
     * Eloquent model that acts as the root model to associate assets to
     *
     * @var \ScubaWhere\Entities\Company
    */ 
    protected $company_model;
    
    public function __construct() {
        $this->company_model = Context::get();
    }

    /**
     * Get all boatrooms for a company
     *
     * @param array $relations
     *
     * @return \Illuminate\Database\Eloquent\Collection
     */
    public function all(array $relations = []) {
        return Boatroom::onlyOwners()->with($relations)->get();
    }

    /**
     * Get all boatrooms for a company including soft deleted models
     *
     * @param array $relations
     *
     * @return \Illuminate\Database\Eloquent\Collection
     */
    public function allWithTrashed(array $relations = []) {
        return Boatroom::onlyOwners()->with($relations)->withTrashed()->get();
    }

    /**
     * Get a boatroom for a company from its id
     *
     * @param int   $id
     * @param array $relations
     * @param bool  $fail
     *
     * @throws \Scubawhere\Exceptions\Http\HttpNotFound
     *
     * @return \ScubaWhere\Entities\Boatroom
     */
    public function get($id, array $relations = [], $fail = true) {
        $boatroom = Boatroom::onlyOwners()->with($relations)->find($id);

        if(is_null($boatroom) && $fail) {
            throw new HttpNotFound(__CLASS__ . __METHOD__, ['The boatroom could not be found']);
        }

        return $boatroom;
    }

    /**
     * Get a boatroom for a company by a specified column and value
     *
     * @param array $query
     * @param array $relations
     * @param bool  $fail
     *
     * @throws \Scubawhere\Exceptions\Http\HttpNotFound
     *
     * @return \ScubaWhere\Entities\Boatroom
     */
    public function getWhere(array $query, array $relations = [], $fail = true) {
        $boatroom = Boatroom::onlyOwners()->where($query)->with($relations)->find();

        if(is_null($boatroom) && $fail) {
            throw new HttpNotFound(__CLASS__ . __METHOD__, ['The boatroom could not be found']);
        }

        return $boatroom;
    }

    /**
     * Create a boatroom and associate it with its company
     *
     * @param array $data
     *
     * @throws \Scubawhere\Exceptions\InvalidInputException
     *
     * @return \ScubaWhere\Entities\Boatroom
     */
    public function create($data)
    {
        $boatroom = new Boatroom($data);

        if (!$boatroom->validate()) {
            throw new InvalidInputException($boatroom->errors()->all());
        }

        return $this->company_model->boatrooms()->save($boatroom);
    }

    /**
     * Update a boatroom
     *
     * @param int   $id
     * @param array $data
     *
     * @return Boatroom
     * @throws HttpNotFound
     * @throws HttpUnprocessableEntity
     */
    public function update($id, $data)
    {
        $boatroom = $this->get($id);
        if(!$boatroom->update($data)) {
            throw new HttpUnprocessableEntity(__CLASS__.__METHOD__, $boatroom->errors()->all());
        }
        return $boatroom;
    }

}
