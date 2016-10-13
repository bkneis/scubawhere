<?php 

namespace ScubaWhere\Repositories;

use ScubaWhere\Context;
use ScubaWhere\Exceptions;
use ScubaWhere\Exceptions\InvalidInputException;
use ScubaWhere\Repositories\CrmTokenRepoInterface;
use ScubaWhere\Exceptions\MethodNotSupportedException;
use Illuminate\Database\Eloquent\ModelNotFoundException;

class CrmTokenRepo implements CrmTokenRepoInterface {

    /** 
     * Eloquent model that acts as the root model to associate assets to
     * @var \Company 
    */ 
    protected $company_model;

    public function __construct() {
        $this->company_model = Context::get();
    }

    /**
     * Get all CrmTokens for a company
     * @throws \ScubaWhere\Exceptions\MethodNotSupportedException
     */
    public function all() {
        throw new MethodNotFoundException();
    }

    /**
     * Get all CrmTokens for a company including soft deleted models
     * @throws \ScubaWhere\Exceptions\MethodNotSupportedException
     */
    public function allWithTrashed() {
        throw new MethodNotFoundException();
    }

    /**
     * Get an CrmToken for a company from its id
     * @param  int   ID of the CrmToken
     * @throws \Illuminate\Database\Eloquent\ModelNotFound
     * @return \CrmToken
     */
    public function get($id) {
        return \CrmToken::findOrFail($id);
    }

    /**
     * Get an CrmToken for a company by a specified column and value
     * @param  string Column name to search by
     * @param  mixed  Value to match the CrmToken
     * @return \Illuminate\Database\Eloquent\Collection
     */
    public function getWhere($query) {
        return \CrmToken::where($query)->get();
    }

    /**
     * Create an crmtoken and associate it with its company
     * @param array Information about the crmtoken to save
     * @throws \ScubaWhere\Exceptions\InvalidInputException
     * @return \Crmtoken
     */
    public function create($campaign_id, $token, $customer_id) {
        $data = array(
            'campaign_id' => $campaign_id,
            'token'       => $token,
            'customer_id' => $customer_id
        );
        
        $crm_token = new \CrmToken($data);
        if (!$crm_token->validate()) {
            throw new InvalidInputException($crm_token->errors()->all());
        }
        $crm_token->save();
        return $crm_token;
    }

    /**
     * Update an CrmToken by id with specified data
     * @param  int   ID of the CrmToken
     * @param  array Data to update the CrmToken with
     * @throws \ScubaWhere\Exceptions\InvalidInputException
     * @return \CrmToken
     */
    public function update($id, $data) {
        $crm_token = $this->get($id);
        if(!$crm_token->update($data)) {
            throw new InvalidInputException($crm_token->errors()->all());
        }
        return $crm_token;
    }

    /**
     * Delete an CrmToken by its id
     * @param  int ID of the CrmToken
     * @throws Exception
     */
    public function delete($id) {
        $crm_token = $this->get($id);
        $crm_token->delete();
    }

    /**
     * Delete an CrmToken by a specified column and value
     * @param  string Column name to search by
     * @param  mixed  Value to match the CrmToken
     * @throws Exception
     */
    public function deleteWhere($query) {
        $crm_token = $this->getWhere($query);
        $crm_token->delete();
    }
}