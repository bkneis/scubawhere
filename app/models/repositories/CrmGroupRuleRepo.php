<?php 

namespace ScubaWhere\Repositories;

use ScubaWhere\Context;
use ScubaWhere\Exceptions;
use ScubaWhere\Exceptions\InvalidInputException;
use ScubaWhere\Exceptions\MethodNotFoundException;
use ScubaWhere\Repositories\CrmgroupruleRepoInterface;
use Illuminate\Database\Eloquent\ModelNotFoundException;

class CrmGroupRuleRepo /*extends BaseRepo*/ implements CrmGroupRuleRepoInterface {

    /** 
     * Eloquent model that acts as the root model to associate assets to
     * @var \Company 
    */ 
    protected $company_model;

    public function __construct() {
        $this->company_model = Context::get();
    }

    /**
     * Get all crmgrouprules for a company
     * @throws \ScubaWhere\Exceptions\MethodNotSupported
     */
    public function all() {
        throw new MethodNotSupportedException();
    }

    /**
     * Get all crmgrouprules for a company including soft deleted models
     * @throws \ScubaWhere\Exceptions\MethodNotSupported
     */
    public function allWithTrashed() {
        throw new MethodNotSupportedException();
    }

    /**
     * Get an CrmRroupRule for a company from its id
     * @param  int   ID of the CrmGroupRule
     * @throws \Illuminate\Database\Eloquent\ModelNotFound
     * @return \CrmGroupRule
     */
    public function get($id) {
        return \CrmGroupRule::findOrFail($id);
    }

    /**
     * Get a CrmGroupRule for a company by a specified column and value
     * @param  string Column name to search by
     * @param  mixed  Value to match the CrmGroupRule
     * @return \Illuminate\Database\Eloquent\Collection
     */
    public function getWhere($query) {
        return \CrmGroupRule::where($query)->get();
    }

    /**
     * Create an CrmGroupRule and associate it with its company
     * @param array Information about the CrmGroupRule to save
     * @throws \ScubaWhere\Exceptions\InvalidInputException
     * @return \CrmGroupRule
     */
    public function create($data) {
        $crm_group_rule = new \CrmGroupRule($data);
        if (!$crm_group_rule->validate()) {
            throw new InvalidInputException($crm_group_rule->errors()->all());
        }
        return $crm_group_rule;
    }

    /**
     * Update an CrmGroupRule by id with specified data
     * @param  int   ID of the CrmGroupRule
     * @param  array Data to update the CrmGroupRule with
     * @throws \ScubaWhere\Exceptions\InvalidInputException
     * @return \CrmGroupRule
     */
    public function update($id, $data) {
        $crm_group_rule = $this->get($id);
        if(!$crm_group_rule->update($data)) {
            throw new InvalidInputException($crm_group_rule->errors()->all());
        }
        return $crm_group_rule;
    }

    /**
     * Delete an CrmGroupRule by its id
     * @param  int ID of the CrmGroupRule
     * @throws \Exception
     */
    public function delete($id) {
        $crm_group_rule = $this->get($id);
        $crm_group_rule->delete();
    }

    /**
     * Delete an CrmGroupRule by a specified column and value
     * @param  string Column name to search by
     * @param  mixed  Value to match the CrmGroupRule
     * @throws \Exception
     */
    public function deleteWhere($query) {
        $crm_group_rule = $this->getWhere($query);
        $crm_group_rule->delete();
    }
}