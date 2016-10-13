<?php 

namespace ScubaWhere\Repositories;

use ScubaWhere\Context;
use ScubaWhere\Exceptions;
use ScubaWhere\Exceptions\InvalidInputException;
use ScubaWhere\Repositories\CrmCampaignRepoInterface;
use Illuminate\Database\Eloquent\ModelNotFoundException;

class CrmCampaignRepo /*extends BaseRepo*/ implements CrmCampaignRepoInterface {

    /** 
     * Eloquent model that acts as the root model to associate assets to
     * @var \Company 
    */ 
    protected $company_model;

    public function __construct() {
        $this->company_model = Context::get();
    }

    /**
     * Get all CrmCampaigns for a company
     * @return \Illuminate\Database\Eloquent\Collection
     */
    public function all() {
        return \CrmCampaign::onlyOwners()->with('tokens', 'groups', 'crmLinks')->get();
    }

    /**
     * Get all crmcampaigns for a company including soft deleted models
     * @return \Illuminate\Database\Eloquent\Collection 
     */
    public function allWithTrashed() {
        return \CrmCampaign::onlyOwners()->withTrashed()->with('tokens', 'groups', 'crmLinks')->get();
    }

    /**
     * Get an crmcampaign for a company from its id
     * @param  int   ID of the crmcampaign
     * @throws \Illuminate\Database\Eloquent\ModelNotFound
     * @return \CrmCampaign
     */
    public function get($id) {
        return \CrmCampaign::onlyOwners()->with('tokens', 'groups', 'crmLinks')->findOrFail($id);
    }

    /**
     * Get an crmcampaign for a company by a specified column and value
     * @param  string Column name to search by
     * @param  mixed  Value to match the crmcampaign
     * @return \Illuminate\Database\Eloquent\Collection
     */
    public function getWhere($query) {
        return \CrmCampaign::onlyOwners()->where($query)->with('tokens', 'groups', 'crmLinks')->get();
    }

    /**
     * Create an crmcampaign and associate it with its company
     * @param array Information about the crmcampaign to save
     * @throws \ScubaWhere\Exceptions\InvalidInputException
     * @return \CrmCampaign
     */
    public function create($data) {
        $campaign = new \CrmCampaign($data);
        if (!$campaign->validate()) {
            throw new InvalidInputException($campaign->errors()->all());
        }
        return $this->company_model->crmCampaigns()->save($campaign);
    }

    public function restore($id) 
    {
        $campaign = $this->get($id);
        return $campaign->restore();
    }

    /**
     * Delete an crmcampaign by its id
     * @param  int ID of the crmcampaign
     * @throws \Exception
     */
    public function delete($id) {
        $campaign = $this->get($id);
        return $campaign->delete();
    }

    /**
     * Delete an crmcampaign by a specified column and value
     * @param  string Column name to search by
     * @param  mixed  Value to match the crmcampaign
     * @throws \Exception
     */
    public function deleteWhere($query) {
        $campaign = $this->getWhere($query);
        return $campaign->delete();
    }
}