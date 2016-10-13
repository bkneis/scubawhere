<?php 

namespace ScubaWhere\Repositories;

use ScubaWhere\Context;
use ScubaWhere\Exceptions;
use ScubaWhere\Exceptions\InvalidInputException;
use ScubaWhere\Repositories\CrmTemplateRepoInterface;
use Illuminate\Database\Eloquent\ModelNotFoundException;

class CrmTemplateRepo /*extends BaseRepo*/ implements CrmTemplateRepoInterface {

    /** 
     * Eloquent model that acts as the root model to associate assets to
     * @var \Company 
    */ 
    protected $company_model;

    public function __construct() {
        $this->company_model = Context::get();
    }

    /**
     * Get all CrmTemplates for a company
     * @return \Illuminate\Database\Eloquent\Collection
     */
    public function all() {
        return \CrmTemplate::onlyOwners()->get();
    }

    /**
     * Get all CrmTemplates for a company including soft deleted models
     * @return \Illuminate\Database\Eloquent\Collection
     */
    public function allWithTrashed() {
        return \CrmTemplate::onlyOwners()->withTrashed()->get();
    }

    /**
     * Get an CrmTemplate for a company from its id
     * @param  int ID of the CrmTemplate
     * @throws \Illuminate\Database\Eloquent\ModelNotFound
     * @return \CrmTemplate
     */
    public function get($id) {
        return \CrmTemplate::onlyOwners()->findOrFail($id);
    }

    /**
     * Get an CrmTemplate for a company by a specified column and value
     * @param  string Column name to search by
     * @param  mixed  Value to match the CrmTemplate
     * @return \Illuminate\Database\Eloquent\Collection 
     */
    public function getWhere($query) {
        return \CrmTemplate::onlyOwners()->where($column, $value)->get();
    }

    /**
     * Create an CrmTemplate and associate it with its company
     * @param array Information about the CrmTemplate to save
     * @throws \ScubaWhere\Exceptions\InvalidInputException
     * @return \CrmTemplate 
     */
    public function create($data) {
        $crm_template = new \CrmTemplate($data);
        if (!$crm_template->validate()) {
            throw new InvalidInputException($crm_template->errors()->all());
        }
        return $this->company_model->crmTemplates()->save($crm_template);
    }

    /**
     * Update an CrmTemplate by id with specified data
     * @param  int   ID of the CrmTemplate
     * @param  array Data to update the CrmTemplate with
     * @throws \ScubaWhere\Exceptions\InvalidInputException
     * @return \CrmTemplate
     */
    public function update($id, $data) {
        $crm_template = $this->get($id);
        if(!$crm_template->update($data)) {
            throw new InvalidInputException($crm_template->errors()->all());
        }
        return $crm_template;
    }

    /**
     * Delete an CrmTemplate by its id
     * @param  int ID of the crmtemplate
     * @throws Exception
     */
    public function delete($id) {
        $crm_template = $this->get($id);
        $crm_template->delete();
    }

    /**
     * Delete an CrmTemplate by a specified column and value
     * @param  string Column name to search by
     * @param  mixed  Value to match the crmtemplate
     * @throws Exception
     */
    public function deleteWhere($query) {
        $crm_template = $this->getWhere($column, $value);
        $crm_template->delete();
    }
}