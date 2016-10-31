<?php 

namespace Scubawhere\Repositories;

use Scubawhere\Context;
use Scubawhere\Exceptions;
use Scubawhere\Entities\Boat;
use Scubawhere\Exceptions\Http\HttpNotFound;
use Scubawhere\Exceptions\Http\HttpNotAcceptable;

/**
 * Class BoatRepo acts as a DAO for the accommodation models.
 *
 * It should always return eloquent models and can be thought of as a collection. If at any point in the project, you
 * find yourself accessing the model to retrieve the same data, please add a function to this class and use that.
 *
 * @package Scubawhere\Repositories
 *
 * @see \Scubawhere\Repositories\BaseRepo
 * @see \Scubawhere\Repositories\BoatRepoInterface
 */
class BoatRepo extends BaseRepo implements BoatRepoInterface {

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
     * Get all boats for a company
     *
     * @param array $relations
     *
     * @return \Illuminate\Database\Eloquent\Collection
     */
    public function all(array $relations = []) {
        return Boat::onlyOwners()->with($relations)->get();
    }

    /**
     * Get all boats for a company including soft deleted models
     *
     * @param array $relations
     *
     * @return \Illuminate\Database\Eloquent\Collection
     */
    public function allWithTrashed(array $relations = []) {
        return Boat::onlyOwners()->with($relations)->withTrashed()->get();
    }

    /**
     * Get an boat for a company from its id
     *
     * @param int   $id
     * @param array $relations
     * @param bool  $fail
     *
     * @throws \Scubawhere\Exceptions\Http\HttpNotFound
     *
     * @return \ScubaWhere\Entities\Boat
     */
    public function get($id, array $relations = [], $fail = true) {
        $boat = Boat::onlyOwners()->with($relations)->find($id);

        if(is_null($boat) && $fail) {
            throw new HttpNotFound(__CLASS__ . __METHOD__, ['The boat could not be found.'.$id]);
        }

        return $boat;
    }

    /**
     * Get an boat for a company by a specified column and value
     *
     * @param array $query
     * @param array $relations
     * @param bool  $fail
     *
     * @throws \Scubawhere\Exceptions\Http\HttpNotFound
     *
     * @return \ScubaWhere\Entities\Boat
     */
    public function getWhere(array $query, array $relations = [], $fail = true) {
        $boat = Boat::onlyOwners()->where($relations)->with($relations)->find();

        if(is_null($boat) && $fail) {
            throw new HttpNotFound(__CLASS__ . __METHOD__, ['The boat could not be found.']);
        }

        return $boat;
    }


    /**
     * Create an boat and associate it with its company
     *
     * @param array $data Information about the boat to save
     *
     * @throws \Scubawhere\Exceptions\Http\HttpNotAcceptable
     *
     * @return \ScubaWhere\Entities\Boat
     */
    public function create($data) {
        $boat = new Boat($data);

        if (!$boat->validate()) {
            throw new HttpNotAcceptable(__CLASS__.__METHOD__, $boat->errors()->all());
        }

        return $this->company_model->boats()->save($boat);
    }

    /**
     * Update a boat
     *
     * @param int   $id   ID of the boat
     * @param array $data Information about the boat to update
     * @param bool  $fail Whether to fail or not
     *
     * @throws \Scubawhere\Exceptions\Http\HttpNotAcceptable
     *
     * @return \ScubaWhere\Entities\Boat
     */
    public function update($id, array $data, $fail = true) {
        $boat = $this->get($id);

        if(!$boat->update($data)) {
            throw new HttpNotAcceptable(__CLASS__ . __METHOD__, [$boat->errors()->all()]);
        }

        return $boat;
    }

}
