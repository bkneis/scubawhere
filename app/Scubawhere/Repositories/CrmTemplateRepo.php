<?php 

namespace Scubawhere\Repositories;

use Scubawhere\Context;
use Scubawhere\Exceptions;
use Scubawhere\Entities\CrmTemplate;
use Scubawhere\Exceptions\InvalidInputException;
use ScubaWhere\Exceptions\Http\HttpNotFound;
use ScubaWhere\Exceptions\Http\HttpNotAcceptable;

/**
 * Class CrmTemplateRepo acts as a DAO for the accommodation models.
 *
 * It should always return eloquent models and can be thought of as a collection. If at any point in the project, you
 * find yourself accessing the model to retrieve the same data, please add a function to this class and use that.
 *
 * @package Scubawhere\Repositories
 *
 * @see \Scubawhere\Repositories\BaseRepo
 * @see \Scubawhere\Repositories\CrmTemplateRepoInterface
 */
class CrmTemplateRepo extends BaseRepo implements CrmTemplateRepoInterface {

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
     * Get all CrmTemplates for a company
     *
     * @param array $relations
     *
     * @return \Illuminate\Database\Eloquent\Collection
     */
    public function all(array $relations = []) {
        return CrmTemplate::onlyOwners()->with($relations)->get();
    }

    /**
     * Get all CrmTemplates for a company including soft deleted models
     *
     * @param array $relations
     *
     * @return \Illuminate\Database\Eloquent\Collection
     */
    public function allWithTrashed(array $relations = []) {
        return CrmTemplate::onlyOwners()->with($relations)->withTrashed()->get();
    }

    /**
     * Get an CrmTemplate for a company from its id
     *
     * @param int   $id
     * @param array $relations
     * @param bool  $fail
     *
     * @throws \Scubawhere\Exceptions\Http\HttpNotFound
     *
     * @return \ScubaWhere\Entities\CrmTemplate
     */
    public function get($id, array $relations = [], $fail = true) {
        $template = CrmTemplate::with($relations)->find($id);

        if(is_null($template) && $fail) {
            throw new HttpNotFound(__CLASS__ . __METHOD__, ['The email template could not be found']);
        }

        return $template;
    }

    /**
     * Get an CrmTemplate for a company by a specified column and value
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
        $template = CrmTemplate::where($query)->with($relations)->find();

        if(is_null($template) && $fail) {
            throw new HttpNotFound(__CLASS__ . __METHOD__, ['The email template could not be found']);
        }

        return $template;
    }

    /**
     * Create an CrmTemplate and associate it with its company
     *
     * @param array $data Information about the CrmTemplate to save
     *
     * @throws \Scubawhere\Exceptions\InvalidInputException
     *
     * @return \ScubaWhere\Entities\CrmTemplate
     */
    public function create($data) {
        $crm_template = new CrmTemplate($data);

        if (!$crm_template->validate()) {
            throw new InvalidInputException($crm_template->errors()->all());
        }

        return $this->company_model->crmTemplates()->save($crm_template);
    }

    /**
     * Update an Email Template
     *
     * @param int   $id   ID of the addon
     * @param array $data Information about the addon to update
     * @param bool  $fail Whether to fail or not
     *
     * @throws \Scubawhere\Exceptions\Http\HttpNotAcceptable
     *
     * @return \ScubaWhere\Entities\Agent
     */
    public function update($id, array $data, $fail = true) {
        $template = $this->get($id, $fail);

        if(!$template->update($data)) {
            throw new HttpNotAcceptable(__CLASS__ . __METHOD__, [$template->errors()->all()]);
        }

        return $template;
    }

}
