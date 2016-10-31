<?php 

namespace Scubawhere\Repositories;

use Scubawhere\Context;
use Scubawhere\Entities\CrmLinkTracker;
use Scubawhere\Exceptions\Http\HttpNotFound;
use Scubawhere\Exceptions\InvalidInputException;
use ScubaWhere\Exceptions\MethodNotSupportedException;

/**
 * Class CrmLinkTrackerRepo acts as a DAO for the accommodation models.
 *
 * It should always return eloquent models and can be thought of as a collection. If at any point in the project, you
 * find yourself accessing the model to retrieve the same data, please add a function to this class and use that.
 *
 * @package Scubawhere\Repositories
 *
 * @see \Scubawhere\Repositories\BaseRepo
 * @see \Scubawhere\Repositories\CrmLinkTrackerRepoInterface
 */
class CrmLinkTrackerRepo extends BaseRepo implements CrmLinkTrackerRepoInterface {

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
     * Get all CrmLinkTrackers for a company
     *
     * @throws \Scubawhere\Exceptions\MethodNotSupportedException
     */
    public function all(array $relations = []) {
        throw new MethodNotSupportedException([]);
    }

    /**
     * Get all CrmLinkTrackers for a company including soft deleted models
     *
     * @throws \Scubawhere\Exceptions\MethodNotSupportedException
     */
    public function allWithTrashed(array $relations = []) {
        throw new MethodNotSupportedException([]);
    }

    /**
     * Get an CrmLinkTracker for a company from its id
     *
     * @param int   $id
     * @param array $relations
     * @param bool  $fail
     *
     * @throws \ScubaWhere\Exceptions\Http\HttpNotFound
     *
     * @return \ScubaWhere\Entities\CrmLinkTracker
     */
    public function get($id, array $relations = [], $fail = true) {
        $link_tracker = CrmLinkTracker::with($relations)->find($id);

        if(is_null($link_tracker) && $fail) {
            throw new HttpNotFound(__CLASS__ . __METHOD__, ['The user interaction for this link could not be found']);
        }

        return $link_tracker;
    }

    /**
     * Get an CrmLinkTracker for a company by a specified column and value
     *
     * @param array $query
     * @param array $relations
     * @param bool  $fail
     *
     * @throws \ScubaWhere\Exceptions\Http\HttpNotFound
     *
     * @return \Illuminate\Database\Eloquent\Collection
     */
    public function getWhere(array $query = [], array $relations = [], $fail = true) {
        $link_tracker = CrmLinkTracker::where($query)->with($relations)->find();

        if(is_null($link_tracker) && $fail) {
            throw new HttpNotFound(__CLASS__ . __METHOD__, ['The user interaction for this link could not be found']);
        }

        return $link_tracker;
    }

    /**
     * Create an CrmLinkTracker and associate it with its company
     *
     * @param array $data Information about the CrmLinkTracker to save
     *
     * @throws \Scubawhere\Exceptions\InvalidInputException
     *
     * @return \ScubaWhere\Entities\CrmLinkTracker
     */
    public function create($data) {
        $crm_link_tracker = new CrmLinkTracker($data);
        if (!$crm_link_tracker->validate()) {
            throw new InvalidInputException($crm_link_tracker->errors()->all());
        }
        return $crm_link_tracker->save();
    }

}
