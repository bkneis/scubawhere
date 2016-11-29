<?php 

namespace Scubawhere\Repositories;

use Scubawhere\Context;
use Scubawhere\Helper;
use Scubawhere\Exceptions;
use Scubawhere\Entities\Location;
use Scubawhere\Exceptions\InvalidInputException;
use Scubawhere\Exceptions\Http\HttpNotFound;
use Scubawhere\Exceptions\Http\HttpNotAcceptable;

/**
 * Class LocationRepo acts as a DAO for the accommodation models.
 *
 * It should always return eloquent models and can be thought of as a collection. If at any point in the project, you
 * find yourself accessing the model to retrieve the same data, please add a function to this class and use that.
 *
 * @package Scubawhere\Repositories
 *
 * @see \Scubawhere\Repositories\BaseRepo
 * @see \Scubawhere\Repositories\LocationRepoInterface
 */
class LocationRepo extends BaseRepo implements LocationRepoInterface {

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
     * Get all locations for a company
     *
     * @param array $relations
     *
     * @return \Illuminate\Database\Eloquent\Collection
     */
    public function all(array $relations = []) {
        return $this->company_model->locations()->with($relations)->get();
        //return $this->company_model->locations()->with('tags')->get();
    }

    /**
     * Get all locations for a company including soft deleted models
     *
     * @param array $relations
     *
     * @return \Illuminate\Database\Eloquent\Collection
     */
    public function allWithTrashed(array $relations = []) {
        return $this->company_model->locations()->with($relations)->withTrashed()->get();
    }

    /**
     * Get an location for a company from its id
     *
     * @param int   $id
     * @param array $relations
     * @param bool  $fail
     *
     * @throws \Scubawhere\Exceptions\Http\HttpNotFound
     *
     * @return \ScubaWhere\Entities\Location
     */
    public function get($id, array $relations = [], $fail = true) {
        $location = Location::with($relations)->find($id);

        if(is_null($location) && $fail) {
            throw new HttpNotFound(__CLASS__ . __METHOD__, ['The location could not be found']);
        }
        return $location;
    }

    /**
     * Get an location for a company by a specified column and value
     *
     * @param array $query
     * @param array $relations
     * @param bool  $fail
     *
     * @return \ScubaWhere\Entities\Location
     */
    public function getWhere(array $query, array $relations = [], $fail = true) {
        return $this->company_model->locations()->where($query)->with($relations)->get();
    }

    /**
     * Create an location and associate it with its company
     *
     * @param array $data Information about the location to save
     *
     * @throws \Scubawhere\Exceptions\InvalidInputException
     *
     * @return \ScubaWhere\Entities\Location
     */
    public function create($data) {
        $location = new Location($data);

        if (!$location->validate()) {
            throw new InvalidInputException($location->errors()->all());
        }

        $location->save();
        Context::get()->locations()->attach($location->id);
        
        return $location;
    }

    public function update($id, $description)
    {
        $location = $this->get($id);

        $description = Helper::sanitiseBasicTags($description);

        Context::get()->locations()->updateExistingPivot($location->id, ['description' => $description]);

        return $location;
    }

}
