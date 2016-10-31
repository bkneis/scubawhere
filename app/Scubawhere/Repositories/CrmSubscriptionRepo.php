<?php 

namespace Scubawhere\Repositories;

use Scubawhere\Context;
use Scubawhere\Exceptions;
use Scubawhere\Entities\CrmSubscription;
use Scubawhere\Exceptions\InvalidInputException;
use Scubawhere\Exceptions\Http\HttpNotFound;
use ScubaWhere\Exceptions\MethodNotSupportedException;

/**
 * Class CrmSubscriptionRepo acts as a DAO for the accommodation models.
 *
 * It should always return eloquent models and can be thought of as a collection. If at any point in the project, you
 * find yourself accessing the model to retrieve the same data, please add a function to this class and use that.
 *
 * @package Scubawhere\Repositories
 *
 * @see \Scubawhere\Repositories\BaseRepo
 * @see \Scubawhere\Repositories\CrmSubscriptionRepoInterface
 */
class CrmSubscriptionRepo extends BaseRepo implements CrmSubscriptionRepoInterface {

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
     * @param array $relations
     *
     * @throws \Scubawhere\Exceptions\MethodNotSupportedException
     */
    public function all(array $relations = []) {
        throw new MethodNotSupportedException(['error']);
    }

    /**
     * @param array $relations
     *
     * @throws \Scubawhere\Exceptions\MethodNotSupportedException
     */
    public function allWithTrashed(array $relations = []) {
        throw new MethodNotSupportedException(['error']);
    }

    /**
     * Get an crmsubscription for a company from its id
     *
     * @param int   $id
     * @param array $relations
     * @poaram bool $fail
     *
     * @throws \Scubawhere\Exceptions\Http\HttpNotFound
     *
     * @return \ScubaWhere\Entities\CrmSubscription
     */
    public function get($id, array $relations = [], $fail = true) {
        $subscription = CrmSubscription::with($relations)->find($id);

        if(is_null($subscription) && $fail) {
            throw new HttpNotFound(__CLASS__ . __METHOD__, ['The customer subscription could not be found']);
        }

        return $subscription;
    }

    /**
     * Get an CrmSubscription for a company by a specified column and value
     *
     * @param array $query
     * @param array $relations
     * @poaram bool $fail
     *
     * @throws \Scubawhere\Exceptions\Http\HttpNotFound
     *
     * @return \Illuminate\Database\Eloquent\Collection
     */
    public function getWhere(array $query = [], array $relations = [], $fail = true) {
        $subscription = CrmSubscription::where($query)->with($relations)->find();

        if(is_null($subscription) && $fail) {
            throw new HttpNotFound(__CLASS__ . __METHOD__, ['The customer subscription could not be found']);
        }

        return $subscription;
    }

    /**
     * Create an CrmSubscription and associate it with its company
     *
     * @param array $data Information about the CrmSubscription to save
     *
     * @throws \Scubawhere\Exceptions\InvalidInputException
     *
     * @return \ScubaWhere\Entities\CrmSubscription
     */
    public function create($data) {
        $crm_subscription = new CrmSubscription($data);

        if (!$crm_subscription->validate()) {
            throw new InvalidInputException($crm_subscription->errors()->all());
        }

        return $crm_subscription;
    }

}
