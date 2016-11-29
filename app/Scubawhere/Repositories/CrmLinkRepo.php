<?php 

namespace Scubawhere\Repositories;

use Scubawhere\Context;
use Scubawhere\Entities\CrmLink;
use Scubawhere\Exceptions\Http\HttpNotFound;
use Scubawhere\Exceptions\InvalidInputException;
use ScubaWhere\Exceptions\MethodNotSupportedException;

/**
 * Class CrmLinkRepo acts as a DAO for the accommodation models.
 *
 * It should always return eloquent models and can be thought of as a collection. If at any point in the project, you
 * find yourself accessing the model to retrieve the same data, please add a function to this class and use that.
 *
 * @package Scubawhere\Repositories
 *
 * @see \Scubawhere\Repositories\BaseRepo
 * @see \Scubawhere\Repositories\CrmLinkRepoInterface
 */
class CrmLinkRepo extends BaseRepo implements CrmLinkRepoInterface {

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
     * Get all CrmLinks for a company
     *
     * @param array $relations
     *
     * @throws \Scubawhere\Exceptions\MethodNotSupportedException
     */
    public function all(array $relations = []) {
        throw new MethodNotSupportedException(['error']);
    }

    /**
     * Get all CrmLinks for a company including soft deleted models
     *
     * @param array $relations
     *
     * @throws \Scubawhere\Exceptions\MethodNotSupportedException
     */
    public function allWithTrashed(array $relations = []) {
        throw new MethodNotSupportedException(['error']);
    }

    /**
     * Get an CrmLink for a company from its id
     *
     * @param  int $id ID of the CrmLink
     *
     * @throws \Illuminate\Database\Eloquent\ModelNotFoundException
     *
     * @return \ScubaWhere\Entities\CrmLink
     */
    public function get($id, array $relations = [], $fail = true) {
        return CrmLink::onlyOwners()->findOrFail($id);
    }

    /**
     * Get an crmlink for a company by a specified column and value
     *
     * @param array $query
     * @param array $relations
     * @param bool  $fail
     *
     * @throws \Scubawhere\Exceptions\Http\HttpNotFound
     *
     * @return \ScubaWhere\Entities\CrmLink
     */
    public function getWhere(array $query, array $relations = [], $fail = true) {
        $link = CrmLink::onlyOwners()->where($query)->with($relations)->find();

        if(is_null($link) && $fail) {
            throw new HttpNotFound(__CLASS__ . __METHOD__, ['The email link could not be found']);
        }

        return $link;
    }

    /**
     * Create an crmlink and associate it with its company
     *
     * @param array $data Information about the crmlink to save
     *
     * @throws \Scubawhere\Exceptions\InvalidInputException
     *
     * @return \ScubaWhere\Entities\CrmLink
     */
    public function create($data) {
        $crm_link = new CrmLink($data);
        if (!$crm_link->validate()) {
            throw new InvalidInputException($crm_link->errors()->all());
        }
        return $crm_link->save($crm_link);
    }

}
