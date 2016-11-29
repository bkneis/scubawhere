<?php 

namespace Scubawhere\Repositories;

use Scubawhere\Context;
use Scubawhere\Exceptions;
use Scubawhere\Entities\CrmToken;
use Scubawhere\Exceptions\InvalidInputException;
use Scubawhere\Exceptions\Http\HttpNotFound;
use ScubaWhere\Exceptions\MethodNotSupportedException;

class CrmTokenRepo extends BaseRepo implements CrmTokenRepoInterface {

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
     * @throws \Scubawhere\Exceptions\MethodNotSupportedException
     */
    public function all(array $relations = []) {
        throw new MethodNotSupportedException(['error']);
    }

    /**
     * @throws \Scubawhere\Exceptions\MethodNotSupportedException
     */
    public function allWithTrashed(array $relations = []) {
        throw new MethodNotSupportedException(['error']);
    }

    /**
     * Get an CrmToken for a company from its id
     *
     * @param int   $id
     * @param array $relations
     * @param bool  $fail
     *
     * @throws \Scubawhere\Exceptions\Http\HttpNotFound
     *
     * @return \ScubaWhere\Entities\CrmToken
     */
    public function get($id, array $relations = [], $fail = true) {
        $token = CrmToken::with($relations)->find($id);

        if(is_null($token) && $fail) {
            throw new HttpNotFound(__CLASS__ . __METHOD__, ['The email token could not be found']);
        }

        return $token;
    }

    /**
     * Get an CrmToken for a company by a specified column and value
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
        $token = CrmToken::where($query)->with($relations)->find();

        if(is_null($token) && $fail) {
            throw new HttpNotFound(__CLASS__ . __METHOD__, ['The email token could not be found']);
        }

        return $token;
    }

    /**
     * Create an crmtoken and associate it with its company
     *
     * @param int    $campaign_id
     * @param string $token
     * @param int    $customer_id
     *
     * @todo move the params into one data array
     *
     * @throws \Scubawhere\Exceptions\InvalidInputException
     *
     * @return \ScubaWhere\Entities\CrmToken
     */
    public function create($campaign_id, $token, $customer_id) {
        $data = array(
            'campaign_id' => $campaign_id,
            'token'       => $token,
            'customer_id' => $customer_id
        );
        
        $crm_token = new CrmToken($data);
        if (!$crm_token->validate()) {
            throw new InvalidInputException($crm_token->errors()->all());
        }
        $crm_token->save();
        return $crm_token;
    }

}
