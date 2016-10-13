<?php 

namespace ScubaWhere\Repositories;

use ScubaWhere\Context;
use ScubaWhere\Exceptions;
use ScubaWhere\Exceptions\InvalidInputException;
use ScubaWhere\Repositories\CrmLinkRepoInterface;
use Illuminate\Database\Eloquent\ModelNotFoundException;

class CrmLinkRepo implements CrmLinkRepoInterface {

    /** 
     * Eloquent model that acts as the root model to associate assets to
     * @var \Company 
    */ 
    protected $company_model;

    public function __construct() {
        $this->company_model = Context::get();
    }

    /**
     * Get all CrmLinks for a company
     * @throws \ScubaWhere\Exceptions\MethodNotSupported
     */
    public function all() {
        throw new MethodNotSupportedException();
    }

    /**
     * Get all CrmLinks for a company including soft deleted models
     * @throws \ScubaWhere\Exceptions\MethodNotSupported
     */
    public function allWithTrashed() {
        throw new MethodNotSupportedException();
    }

    /**
     * Get an CrmLink for a company from its id
     * @param  int   ID of the CrmLink
     * @throws \Illuminate\Database\Eloquent\ModelNotFound
     * @return \CrmLink
     */
    public function get($id) {
        return \CrmLink::onlyOwners()->findOrFail($id);
    }

    /**
     * Get an crmlink for a company by a specified column and value
     * @param  string Column name to search by
     * @param  mixed  Value to match the CrmLink
     * @return \CrmLink
     */
    public function getWhere($column, $value) {
        return \CrmLink::onlyOwners()->where($column, $value)->get();
    }

    /**
     * Create an crmlink and associate it with its company
     * @param array Information about the crmlink to save
     * @throws \ScubaWhere\Exceptions\InvalidInputException
     * @return \CrmLink
     */
    public function create($data) {
        $crm_link = new \Crmlink($data);
        if (!$crm_link->validate()) {
            throw new InvalidInputException($crm_link->errors()->all());
        }
        return $crm_link->save($crm_link);
    }

    /**
     * Update an CrmLink by id with specified data
     * @param  int   ID of the CrmLink
     * @param  array Data to update the CrmLink with
     * @throws \ScubaWhere\Exceptions\InvalidInputException
     * @return \CrmLink
     */
    public function update($id, $data) {
        $crm_link = $this->get($id);
        if(!$crm_link->update($data)) {
            throw new InvalidInputException($crm_link->errors()->all());
        }
        return $crm_link;
    }

    /**
     * Delete an crmlink by its id
     * @param  int ID of the crmlink
     * @throws Exception
     */
    public function delete($id) {
        $crmlink = $this->get($id);
        $crmlink->delete();
    }

    /**
     * Delete an crmlink by a specified column and value
     * @param  string Column name to search by
     * @param  mixed  Value to match the crmlink
     * @throws Exception
     */
    public function deleteWhere($column, $value) {
        $crmlink = $this->getWhere($column, $value);
        $crmlink->delete();
    }
}