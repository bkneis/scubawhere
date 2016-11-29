<?php 

namespace Scubawhere\Repositories;

use Scubawhere\Context;
use Scubawhere\Exceptions;
use Scubawhere\Entities\CrmGroup;
use Scubawhere\Exceptions\Http\HttpNotFound;
use Scubawhere\Exceptions\InvalidInputException;

/**
 * Class CrmGroupRepo acts as a DAO for the accommodation models.
 *
 * It should always return eloquent models and can be thought of as a collection. If at any point in the project, you
 * find yourself accessing the model to retrieve the same data, please add a function to this class and use that.
 *
 * @package Scubawhere\Repositories
 *
 * @see \Scubawhere\Repositories\BaseRepo
 * @see \Scubawhere\Repositories\CrmGroupRepoInterface
 */
class CrmGroupRepo extends BaseRepo implements CrmGroupRepoInterface {

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
     * Get all CrmGroups for a company
     *
     * @param array $relations
     *
     * @return \Illuminate\Database\Eloquent\Collection
     */
    public function all(array $relations = []) {
        return CrmGroup::onlyOwners()->with($relations)->get();
    }

    /**
     * Get all CrmGroups for a company including soft deleted models
     *
     * @param array $relations
     *
     * @return \Illuminate\Database\Eloquent\Collection 
     */
    public function allWithTrashed(array $relations = []) {
        return CrmGroup::onlyOwners()->with($relations)->withTrashed()->get();
    }

    /**
     * Get an CrmGroup for a company from its id
     *
     * @param int   $id
     * @param array $relations
     * @param bool  $fail
     *
     * @throws \Scubawhere\Exceptions\Http\HttpNotFound
     *
     * @return \ScubaWhere\Entities\CrmGroup
     */
    public function get($id, array $relations = [], $fail = true) {
        $group = CrmGroup::onlyOwners()->with($relations)->find($id);

        if(is_null($group) && $fail) {
            throw new HttpNotFound(__CLASS__ . __METHOD__, ['The email group could not be found']);
        }

        return $group;
    }

    /**
     * Get an CrmGroup for a company by a specified column and value
     *
     * @param array $query
     * @param array $relations
     * @param bool  $fail
     *
     * @throws \Scubawhere\Exceptions\Http\HttpNotFound
     *
     * @return \Illuminate\Database\Eloquent\Collection
     */
    public function getWhere(array $query, array $relations = [], $fail = true) {
        $group = CrmGroup::onlyOwners()->where($relations)->with($relations)->find();

        if(is_null($group) && $fail) {
            throw new HttpNotFound(__CLASS__ . __METHOD__, ['The email group could not be found']);
        }

        return $group;
        //return CrmGroup::onlyOwners()->where($query)->with('rules')->get();
    }

    /**
     * Create an CrmGroup and associate it with its company
     *
     * @param array $data Information about the CrmGroup to save
     *
     * @throws \Scubawhere\Exceptions\InvalidInputException
     *
     * @return \ScubaWhere\Entities\CrmGroup
     */
    public function create($data) {
        $crm_group = new CrmGroup($data);

        if (!$crm_group->validate()) {
            throw new InvalidInputException($crm_group->errors()->all());
        }
        
        return $this->company_model->crmGroups()->save($crm_group);
    }

}

