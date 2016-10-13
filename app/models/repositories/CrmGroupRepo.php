<?php 

namespace ScubaWhere\Repositories;

use ScubaWhere\Context;
use ScubaWhere\Exceptions;
use ScubaWhere\Exceptions\InvalidInputException;
use ScubaWhere\Repositories\CrmgroupRepoInterface;
use Illuminate\Database\Eloquent\ModelNotFoundException;

class CrmGroupRepo /*extends BaseRepo*/ implements CrmGroupRepoInterface {

    /** 
     * Eloquent model that acts as the root model to associate assets to
     * @var \Company 
    */ 
    protected $company_model;

    public function __construct() {
        $this->company_model = Context::get();
    }

    /**
     * Get all CrmGroups for a company
     * @return \Illuminate\Database\Eloquent\Collection
     */
    public function all() {
        return \CrmGroup::onlyOwners()->with('rules')->get();
    }

    /**
     * Get all CrmGroups for a company including soft deleted models
     * @return \Illuminate\Database\Eloquent\Collection 
     */
    public function allWithTrashed() {
        return \CrmGroup::onlyOwners()->with('rules')->withTrashed()->get();
    }

    /**
     * Get an CrmGroup for a company from its id
     * @param  int ID of the CrmGroup
     * @throws \Illuminate\Database\Eloquent\ModelNotFound
     * @return \CrmGroup
     */
    public function get($id) {
        return \CrmGroup::onlyOwners()->with('rules')->findOrFail($id);
    }

    /**
     * Get an CrmGroup for a company by a specified column and value
     * @param  string Column name to search by
     * @param  mixed  Value to match the CrmGroup
     * @return \Illuminate\Database\Eloquent\Collection
     */
    public function getWhere($query) {
        return \CrmGroup::onlyOwners()->where($query)->with('rules')->get();
    }

    /**
     * Create an CrmGroup and associate it with its company
     * @param array Information about the CrmGroup to save
     * @throws \ScubaWhere\Exceptions\InvalidInputException
     * @return \CrmGroup
     */
    public function create($data) {
        $crm_group = new \CrmGroup($data);
        if (!$crm_group->validate()) {
            throw new InvalidInputException($crm_group->errors()->all());
        }
        return $this->company_model->crmGroups()->save($crm_group);
    }

    /**
     * Update an CrmGroup by id with specified data
     * @param  int   ID of the CrmGroup
     * @param  array Data to update the CrmGroup with
     * @throws \ScubaWhere\Exceptions\InvalidInputException
     * @return \CrmGroup
     */
    public function update($id, $data) {
        $crm_group = $this->get($id);
        if(!$crm_group->update($data)) {
            throw new InvalidInputException($crm_group->errors()->all());
        }
        return $crm_group;
    }

    /**
     * Delete an CrmGroup by its id
     * @param  int ID of the CrmGroup
     * @throws \Exception
     */
    public function delete($id) {
        $crm_group = $this->get($id);
        $crm_group->delete();
    }

    /**
     * Delete an CrmGroup by a specified column and value
     * @param  string Column name to search by
     * @param  mixed  Value to match the CrmGroup
     * @throws \Exception
     */
    public function deleteWhere($query) {
        $crm_group = $this->getWhere($query);
        $crm_group->delete();
    }
}