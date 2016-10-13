<?php 

namespace ScubaWhere\Repositories;

use ScubaWhere\Context;
use ScubaWhere\Exceptions;
use ScubaWhere\Exceptions\InvalidInputException;
use ScubaWhere\Repositories\CrmSubscriptionRepoInterface;
use Illuminate\Database\Eloquent\ModelNotFoundException;

class CrmSubscriptionRepo /*extends BaseRepo*/ implements CrmSubscriptionRepoInterface {

    /** 
     * Eloquent model that acts as the root model to associate assets to
     * @var \Company 
    */ 
    protected $company_model;

    public function __construct() {
        $this->company_model = Context::get();
    }

    /**
     * Get all CrmSubscriptions for a company
     * @throws \ScubaWhere\Exceptions\MethodNotSupportedException
     * @return \Illuminate\Database\Eloquent\Collection
     */
    public function all() {
        throw new MethodNotSupportedException();
    }

    /**
     * Get all crmsubscriptions for a company including soft deleted models
     * @throws \ScubaWhere\Exceptions\MethodNotSupportedException
     */
    public function allWithTrashed() {
        throw new MethodNotSupportedException();
    }

    /**
     * Get an crmsubscription for a company from its id
     * @param  int   ID of the CrmSubscription
     * @throws \Illuminate\Database\Eloquent\ModelNotFound
     * @return \CrmSubscription
     */
    public function get($id) {
        return \CrmSubscription::findOrFail($id);
    }

    /**
     * Get an CrmSubscription for a company by a specified column and value
     * @param  string Column name to search by
     * @param  mixed  Value to match the CrmSubscription
     * @return \Illuminate\Database\Eloquent\Collection
     */
    public function getWhere($query) {
        return \CrmSubscription::where($query)->get();
    }

    /**
     * Create an CrmSubscription and associate it with its company
     * @param array Information about the CrmSubscription to save
     * @throws \ScubaWhere\Exceptions\InvalidInputException
     * @return \CrmSubscription
     */
    public function create($data) {
        $crm_subscription = new \CrmSubscription($data);
        if (!$crm_subscription->validate()) {
            throw new InvalidInputException($crm_subscription->errors()->all());
        }
        return $crm_subscription;
    }

    /**
     * Update an CrmSubscription by id with specified data
     * @param  int   ID of the CrmSubscription
     * @param  array Data to update the CrmSubscription with
     * @throws \ScubaWhere\Exceptions\InvalidInputException
     * @return \CrmSubscription
     */
    public function update($id, $data) {
        $crm_subscription = $this->get($id);
        if(!$crm_subscription->update($data)) {
            throw new InvalidInputException($crm_subscription->errors()->all());
        }
        return $crm_subscription;
    }

    /**
     * Delete an CrmSubscription by its id
     * @param  int ID of the CrmSubscription
     * @throws \Exception
     */
    public function delete($id) {
        $crm_subscription = $this->get($id);
        $crm_subscription->delete();
    }

    /**
     * Delete an CrmSubscription by a specified column and value
     * @param  string Column name to search by
     * @param  mixed  Value to match the CrmSubscription
     * @throws \Exception
     */
    public function deleteWhere($query) {
        $crm_subscription = $this->getWhere($query);
        $crm_subscription->delete();
    }
}