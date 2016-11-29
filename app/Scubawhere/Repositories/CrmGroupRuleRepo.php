<?php 

namespace Scubawhere\Repositories;

use Scubawhere\Context;
use Scubawhere\Exceptions;
use Scubawhere\Entities\CrmGroupRule;
use Scubawhere\Exceptions\Http\HttpNotFound;
use Scubawhere\Exceptions\InvalidInputException;
use Scubawhere\Exceptions\MethodNotSupportedException;

class CrmGroupRuleRepo extends BaseRepo implements CrmGroupRuleRepoInterface {

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
     * Get all crmgrouprules for a company
     *
     * @param array $relations
     *
     * @throws \Scubawhere\Exceptions\MethodNotSupportedException
     */
    public function all(array $relations = []) {
        throw new MethodNotSupportedException(['error']);
    }

    /**
     * Get all crmgrouprules for a company including soft deleted models
     *
     * @param array $relations
     *
     * @throws \Scubawhere\Exceptions\MethodNotSupportedException
     */
    public function allWithTrashed(array $relations = []) {
        throw new MethodNotSupportedException(['error']);
    }

    /**
     * Get an CrmRroupRule for a company from its id
     *
     * @param  int $id ID of the CrmGroupRule
     *
     * @throws \Scubawhere\Exceptions\Http\HttpNotFound
     *
     * @return \ScubaWhere\Entities\CrmGroupRule
     */
    public function get($id, array $relations = [], $fail = true) {
        $rule = CrmGroupRule::with($relations)->find($id);

        if(is_null($rule) && $fail) {
            throw new HttpNotFound(__CLASS__ . __METHOD__, ['The email group rule could not be found']);
        }

        return $rule;
    }

    /**
     * Get a CrmGroupRule for a company by a specified column and value
     *
     * @param array $query
     * @param array $relations
     * @param bool  $fail
     *
     * @throws \Scubawhere\Exceptions\Http\HttpNotFound
     *
     * @return \Illuminate\Database\Eloquent\Collection
     */
    public function getWhere(array $query = [], array $relations = [], $fail = true) {
        $rule = CrmGroupRule::where($query)->with($relations)->find();

        if(is_null($rule) && $fail) {
            throw new HttpNotFound(__CLASS__ . __METHOD__, ['The email group rule could not be found']);
        }

        return $rule;
    }

    /**
     * Create an CrmGroupRule and associate it with its company
     *
     * @param array $data Information about the CrmGroupRule to save
     *
     * @throws \Scubawhere\Exceptions\InvalidInputException
     *
     * @return \ScubaWhere\Entities\CrmGroupRule
     */
    public function create($data) {
        $crm_group_rule = new CrmGroupRule($data);

        if (!$crm_group_rule->validate()) {
            throw new InvalidInputException($crm_group_rule->errors()->all());
        }
        
        return $crm_group_rule;
    }

}
